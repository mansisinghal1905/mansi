
   document.addEventListener('DOMContentLoaded', function () {
      const checkbox = document.getElementById('toggle-checkbox');
      const hiddenContent = document.getElementById('edit_bill_recurring_toggle');

      // Initially hide the content
      hiddenContent.style.display = 'none';

      checkbox.addEventListener('change', function () {
         if (checkbox.checked) {
            hiddenContent.style.display = 'block';
         } else {
            hiddenContent.style.display = 'none';
         }
      });
   });




