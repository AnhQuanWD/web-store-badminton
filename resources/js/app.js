import './bootstrap';
import 'preline';

document.addEventListener('livewire:navigated', () => { 
    window.HSStaticMethods.autoInit();
})

$(document).ready(function() {
    $(".button-filter").click(function() {
        $(".container-filter").toggle();
    });

    $('#viewInvoiceBtn').on('click', function() {
        $('#invoicePopup').removeClass('hidden').addClass('flex'); // Show the popup and make it a flex container
    });

    // Close popup when the close button is clicked
    $('#closePopup').on('click', function() {
        $('#invoicePopup').removeClass('flex').addClass('hidden'); // Hide the popup and remove flex layout
    });

    // Close popup if clicking outside the popup content
    $(document).on('click', function(event) {
        if ($(event.target).closest('#invoicePopup section').length === 0 && !$(event.target).is('#viewInvoiceBtn')) {
            $('#invoicePopup').removeClass('flex').addClass('hidden'); // Hide the popup if click outside
        }
    });
});
