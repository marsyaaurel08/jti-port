import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)
Alpine.plugin(intersect)

window.Alpine = Alpine;
Alpine.start();
