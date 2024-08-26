import './bootstrap';
import 'preline';

document.addEventListener('livewire:navigated', () => { 
    window.HSStaticMethods.autoInit();
})

$(document).ready(function() {
    $(".button-filter").click(function() {
        $(".container-filter").toggle();
    });
});
