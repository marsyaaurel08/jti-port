<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Exports\BookExporter;
use App\Filament\Imports\BookImporter;
use App\Filament\Resources\Books\BooksResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListBooks extends ListRecords
{
    protected static string $resource = BooksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(BookImporter::class),
            ExportAction::make()
                ->exporter(BookExporter::class),
        ];
    }
}
