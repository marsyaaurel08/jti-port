import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)
Alpine.plugin(intersect)

window.Alpine = Alpine;

// Only start Alpine if it hasn't been started yet
// This prevents the "multiple instances of Alpine running" warning during HMR
if (!window.Alpine.started) {
    Alpine.start();
    window.Alpine.started = true;
}
