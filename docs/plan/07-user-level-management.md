# Plan 07: Super Admin User Level Management

## Overview
Enable super admins to assign and manage user roles (levels) through the Filament admin panel's User resource. This feature builds on the existing role-based access control system implemented via Filament Shield.

## Current State
- Filament Shield (Spatie Permission) is installed and configured
- User model has `HasRoles` trait from Spatie Permission
- Four roles exist: `super_admin`, `admin`, `editor`, `user`
- User resource exists but lacks role management UI
- UserForm only has: name, email, password fields
- Roles can currently only be assigned via Tinker or seeder

## Requirements
- Super admins can view and change user roles in the admin panel
- Role selector appears in user creation and editing forms
- Current roles are displayed in the user list table
- Only users with super_admin role can manage roles
- Users cannot remove their own super_admin role (safety measure)
- Non-super-admins cannot assign the super_admin role
- Role changes are logged for audit purposes

## Implementation Steps

### Step 1: Add Role Selector to User Form
**File**: `app/Filament/Resources/Users/Schemas/UserForm.php`

**Actions**:
1. Import necessary classes for Select component and role management
2. Add a Select component for roles after the password field
3. Configure the select to support multiple role selection
4. Load available roles from the database
5. Add visibility conditions based on user permissions
6. Style role badges with distinct colors

**Code Changes**:
```php
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->password()
                ->required(fn (string $context): bool => $context === 'create')
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)),

            Select::make('roles')
                ->label('User Level (Roles)')
                ->multiple()
                ->relationship('roles', 'name')
                ->options(function () {
                    $user = Auth::user();
                    $roles = Role::all()->pluck('name', 'id');

                    // If user is not super_admin, remove super_admin from options
                    if (!$user->hasRole('super_admin')) {
                        $superAdminRole = Role::where('name', 'super_admin')->first();
                        if ($superAdminRole) {
                            $roles = $roles->except($superAdminRole->id);
                        }
                    }

                    return $roles;
                })
                ->preload()
                ->searchable()
                ->helperText('Select one or more roles to assign to this user')
                ->visible(fn () => Auth::user()->hasRole('super_admin'))
                ->required(),
        ]);
}
```

**Technical Notes**:
- Use `relationship()` method to work with Spatie's many-to-many relationship
- Filter out super_admin role for non-super-admins
- Make field visible only to super_admins
- Use `preload()` for better UX with small role lists

### Step 2: Handle Role Assignment on User Creation
**File**: `app/Filament/Resources/Users/Pages/CreateUser.php`

**Actions**:
1. Read the current CreateUser page implementation
2. Override `afterCreate()` hook if needed
3. Handle role assignment using Spatie's `syncRoles()` method
4. Add validation to prevent role manipulation

**Code Changes**:
```php
use Illuminate\Database\Eloquent\Model;

protected function afterCreate(): void
{
    // Roles are automatically synced by Filament's relationship handling
    // This hook is for additional logic if needed (e.g., logging, notifications)

    $user = $this->record;

    // Log role assignment for audit trail
    activity()
        ->performedOn($user)
        ->causedBy(auth()->user())
        ->withProperties([
            'roles' => $user->roles->pluck('name')->toArray()
        ])
        ->log('User created with roles');
}
```

**Technical Notes**:
- Filament's relationship method handles syncing automatically
- `afterCreate()` is for additional business logic
- Consider using Laravel Activity Log for audit trail

### Step 3: Handle Role Updates for Existing Users
**File**: `app/Filament/Resources/Users/Pages/EditUser.php`

**Actions**:
1. Read the current EditUser page implementation
2. Override `afterSave()` or `afterUpdate()` hook
3. Prevent users from removing their own super_admin role
4. Log role changes for audit purposes

**Code Changes**:
```php
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

protected function afterSave(): void
{
    $user = $this->record;
    $currentUser = auth()->user();

    // Prevent self-demotion from super_admin
    if ($user->id === $currentUser->id && $currentUser->hasRole('super_admin')) {
        if (!$user->hasRole('super_admin')) {
            $user->assignRole('super_admin');

            Notification::make()
                ->warning()
                ->title('Cannot remove your own super admin role')
                ->body('Your super admin role has been restored for security.')
                ->send();
        }
    }

    // Log role changes for audit trail
    activity()
        ->performedOn($user)
        ->causedBy($currentUser)
        ->withProperties([
            'roles' => $user->roles->pluck('name')->toArray()
        ])
        ->log('User roles updated');
}

protected function mutateFormDataBeforeFill(array $data): array
{
    // Preload existing roles into the form
    $data['roles'] = $this->record->roles->pluck('id')->toArray();

    return $data;
}
```

**Technical Notes**:
- Add safety check to prevent self-demotion
- Use Filament notifications for user feedback
- `mutateFormDataBeforeFill()` ensures roles are shown when editing

### Step 4: Display Roles in User Table
**File**: `app/Filament/Resources/Users/Tables/UsersTable.php`

**Actions**:
1. Read the current UsersTable implementation
2. Add a roles column with badge styling
3. Add role-based filtering
4. Make roles searchable

**Code Changes**:
```php
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Spatie\Permission\Models\Role;

public static function configure(Table $table): Table
{
    return $table
        ->columns([
            // ... existing columns

            TextColumn::make('roles.name')
                ->label('Roles')
                ->badge()
                ->colors([
                    'danger' => 'super_admin',
                    'warning' => 'admin',
                    'success' => 'editor',
                    'gray' => 'user',
                ])
                ->searchable()
                ->sortable(),

            TextColumn::make('email')
                ->searchable()
                ->sortable(),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            SelectFilter::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->label('Filter by Role'),
        ])
        ->actions([
            // ... existing actions
        ]);
}
```

**Technical Notes**:
- Use `badge()` for visual role representation
- Assign distinct colors per role type
- Add role-based filtering for easier user management
- Make roles searchable for quick access

### Step 5: Add Permission Checks to User Resource
**File**: `app/Policies/UserPolicy.php` (should already exist from Shield)

**Actions**:
1. Verify existing policy methods
2. Ensure role management is restricted to super_admins
3. Add custom authorization logic if needed

**Verification**:
```php
public function update(User $user, User $model): bool
{
    // Allow update if user has permission
    // Shield should handle this automatically
    return $user->can('update:User');
}

// Custom method to check role management permission
public function manageRoles(User $user): bool
{
    return $user->hasRole('super_admin');
}
```

**Technical Notes**:
- Shield policies should already restrict access
- Custom `manageRoles()` method for explicit checks
- Use this in form field visibility conditions

### Step 6: Optional - Add Activity Logging
**Optional Dependency**: `spatie/laravel-activitylog`

**Installation** (if not present):
```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

**Configure User Model**:
```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'roles.name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

**Technical Notes**:
- This is optional but highly recommended for audit trails
- Logs all role changes automatically
- Can be viewed in a separate ActivityLog resource

### Step 7: Testing

**Manual Testing Checklist**:

1. **As Super Admin**:
   - [ ] Create new user with roles
   - [ ] Edit existing user and change roles
   - [ ] View user table and see role badges
   - [ ] Filter users by role
   - [ ] Search users by role name
   - [ ] Assign super_admin role to another user
   - [ ] Try to remove own super_admin role (should fail)

2. **As Admin** (non-super-admin):
   - [ ] Try to access user edit form
   - [ ] Verify role selector is hidden
   - [ ] Verify cannot assign roles

3. **As Editor/User**:
   - [ ] Verify cannot access user management at all
   - [ ] Verify UserPolicy blocks access

4. **Edge Cases**:
   - [ ] Create user without roles (should require at least one)
   - [ ] Assign multiple roles to user
   - [ ] Remove all roles except one
   - [ ] Check database role assignments match UI

**Automated Testing** (optional):
Create `tests/Feature/UserRoleManagementTest.php`:
```php
use App\Models\User;
use Spatie\Permission\Models\Role;

test('super admin can assign roles to users', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');

    $targetUser = User::factory()->create();
    $editorRole = Role::where('name', 'editor')->first();

    $this->actingAs($superAdmin);

    Livewire::test(EditUser::class, ['record' => $targetUser->id])
        ->set('data.roles', [$editorRole->id])
        ->call('save')
        ->assertHasNoErrors();

    expect($targetUser->fresh()->hasRole('editor'))->toBeTrue();
});

test('user cannot remove their own super admin role', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');

    $this->actingAs($superAdmin);

    Livewire::test(EditUser::class, ['record' => $superAdmin->id])
        ->set('data.roles', [Role::where('name', 'editor')->first()->id])
        ->call('save');

    expect($superAdmin->fresh()->hasRole('super_admin'))->toBeTrue();
});

test('non super admin cannot see role selector', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $targetUser = User::factory()->create();

    $this->actingAs($admin);

    Livewire::test(EditUser::class, ['record' => $targetUser->id])
        ->assertFormFieldIsHidden('roles');
});
```

**Run Tests**:
```bash
php artisan test --filter=UserRoleManagement
```

### Step 8: Documentation Updates

**Update CLAUDE.md** (if needed):
Add notes about role management feature:
```markdown
### User Role Management
Super admins can assign roles to users through the User resource:
- Navigate to Admin Panel > Users
- Create or edit a user
- Select roles from the "User Level (Roles)" dropdown
- Role badges appear in the user list table
- Filter users by role using the table filter
```

## Dependencies
- **Existing**: `spatie/laravel-permission` (already installed via Shield)
- **Existing**: `bezhansalleh/filament-shield` (already installed)
- **Optional**: `spatie/laravel-activitylog` (for audit logging)

## Configuration Files
No new configuration files needed. Uses existing:
- `config/filament-shield.php`
- `config/permission.php`

## Database Changes
No new migrations required. Uses existing Spatie Permission tables:
- `model_has_roles` - Stores user-role relationships

## Security Considerations
1. **Permission Checks**: Only super_admins can manage roles
2. **Self-Demotion Prevention**: Users cannot remove their own super_admin role
3. **Role Restriction**: Non-super-admins cannot assign super_admin role
4. **Audit Trail**: All role changes should be logged
5. **Policy Enforcement**: UserPolicy ensures authorization at resource level

## Performance Considerations
1. **Eager Loading**: Roles are eager loaded in table queries
2. **Caching**: Spatie Permission caches role/permission checks
3. **Preloading**: Role options are preloaded in select component
4. **Indexing**: `model_has_roles` table has proper indexes

## Success Criteria
- [ ] Role selector appears in user form for super_admins
- [ ] Role selector is hidden for non-super-admins
- [ ] Roles can be assigned during user creation
- [ ] Roles can be updated for existing users
- [ ] Role badges appear in user table
- [ ] Role filtering works in user table
- [ ] Role searching works in user table
- [ ] Super_admin role cannot be assigned by non-super-admins
- [ ] Users cannot remove their own super_admin role
- [ ] Role changes are logged (if activity log implemented)
- [ ] All tests pass
- [ ] No permission bypass vulnerabilities

## Implementation Checklist

### Phase 1: Form Updates
- [ ] Update UserForm.php with role selector
- [ ] Add role relationship configuration
- [ ] Add visibility conditions for super_admins
- [ ] Add role filtering logic

### Phase 2: Page Hooks
- [ ] Update CreateUser.php with afterCreate hook
- [ ] Update EditUser.php with afterSave hook
- [ ] Add self-demotion prevention logic
- [ ] Add user notifications for prevented actions

### Phase 3: Table Updates
- [ ] Add roles column to UsersTable.php
- [ ] Configure role badges with colors
- [ ] Add role filter
- [ ] Make roles searchable

### Phase 4: Testing
- [ ] Manual testing as super_admin
- [ ] Manual testing as admin
- [ ] Manual testing as editor/user
- [ ] Edge case testing
- [ ] Optional: Write automated tests

### Phase 5: Documentation
- [ ] Update this plan with implementation notes
- [ ] Update CLAUDE.md if needed
- [ ] Document any issues encountered

## Rollback Plan
If issues occur:
1. Revert UserForm.php changes (remove role selector)
2. Revert CreateUser.php changes (remove hooks)
3. Revert EditUser.php changes (remove hooks)
4. Revert UsersTable.php changes (remove role column)
5. Run `php artisan optimize:clear`
6. Roles can still be managed via Tinker as before

## Estimated Effort
- Form updates: 30 minutes
- Page hooks: 45 minutes
- Table updates: 30 minutes
- Testing: 1 hour
- Documentation: 15 minutes
- **Total**: ~3 hours

## Implementation Notes
(To be filled during/after implementation)

### What Was Implemented
- TBD

### Issues Encountered
- TBD

### Deviations from Plan
- TBD

## References
- Filament Forms: https://filamentphp.com/docs/4.x/forms/fields/select
- Filament Tables: https://filamentphp.com/docs/4.x/tables/columns/text
- Spatie Permission: https://spatie.be/docs/laravel-permission
- Filament Shield: https://github.com/bezhanSalleh/filament-shield
