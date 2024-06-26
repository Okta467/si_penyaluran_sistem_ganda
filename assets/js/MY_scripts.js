/**
 * Initialize select2 and change select2 default styling to sb admin pro styling
 * 
 * Always remember to put option `dropdownParent` especially if select2
 * is inside modal. Without this option, select2 will be buggy
 * 
 * @param {JQuery} selector - A jQuery selector to identify the elements to operate on.
 * @param {Object} options - The Select2 options
 */
function initSelect2(selector, options) {
    selector = selector ?? $('.select2');

    options = options ?? {
        width: '100%',
        dropdownParent: ".modal-content .modal-body"
    }

    selector.select2(options);

    document.querySelectorAll('.select2-container .select2-selection--single').forEach(function (element) {
        element.style.height = 'auto';
    });

    document.querySelectorAll('.select2-container--default .select2-selection--single').forEach(function (element) {
        element.style.border = '1px solid #c5ccd6';
        element.style.webkitAppearance = 'none';
        element.style.mozAppearance = 'none';
        element.style.appearance = 'none';
        element.classList.add('form-control', 'p-0', 'py-2');
    });

    document.querySelectorAll('.select2-container--default .select2-selection--single .select2-selection__rendered').forEach(function (element) {
        element.style.lineHeight = '28px';
        element.classList.add('ms-2');
    });

    document.querySelectorAll('.select2-container--default .select2-selection--single .select2-selection__arrow').forEach(function (element) {
        element.classList.add('mt-2');
    });
}

/**
 * Show/hide value of password input
 * 
 * @param {*} elPassword password element
 */
function initTogglePassword(elPassword) {
    let password = elPassword;

    password.getAttribute('type') === 'password' ?
        password.setAttribute('type', 'text') :
        password.setAttribute('type', 'password');
}

/**
 * Check if two password input is the same
 * 
 * Show help (notice text below input) element if true, hide if not
 * 
 * @param {*} elPassword 
 * @param {*} elPasswordConfirm 
 * @param {*} elPasswordHelp
 * @param {*} elPasswordConfirmHelp
 */
function initIsPasswordSame(elPassword, elPasswordConfirm, elPasswordHelp, elPasswordConfirmHelp) {
    let password = elPassword;
    let passwordConfirm = elPasswordConfirm;

    let passwordHelp = elPasswordHelp;
    let passwordConfirmHelp = elPasswordConfirmHelp;

    if (password.value !== passwordConfirm.value) {
        passwordHelp.classList.remove('d-none');
        passwordConfirmHelp.classList.remove('d-none');
    } else {
        passwordHelp.classList.add('d-none');
        passwordConfirmHelp.classList.add('d-none');
    }
}


/**
 * Toggle (show) sweetalert2 confirmation when form inside modal is submitted
 * 
 * @param {JQuery} selector - A jQuery selector to identify the elements to operate on.
 * @param {string} event - One or more space-separated event types and optional namespaces, such as "click" or "keydown.myPlugin".
 */
function toggleSwalSubmit(selector, event) {

    selector.on(event, function (e) {
        e.preventDefault();
        var form = $(this).parents('.modal-content').find('form');

        // Validate form before showing sweetalert
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
        } else {
            Swal.fire({
                title: "Konfirmasi Tindakan?",
                text: "Harap perhatikan kembali input sebelum submit.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, konfirmasi!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Tindakan Dikonfirmasi!",
                        text: "Halaman akan di-reload untuk memproses.",
                        icon: "success",
                        timer: 3000
                    }).then(() => {
                        form.submit();
                    });
                }
            });
        }
    });
}


/**
 * Print a page in pop-up window with width and height based on user device
 * 
 * @param {string} url
 */
function printExternal(url) {
  // Get the screen width and height
  var screenWidth = screen.width;
  var screenHeight = screen.height;
  
  // Open a new window with the dimensions of the user's screen
  var printWindow = window.open(url, 'Print', `left=0, top=0, width=${screenWidth}, height=${screenHeight}, toolbar=0, resizable=0`);

  printWindow.addEventListener('load', function() {
    if (Boolean(printWindow.chrome)) {
      printWindow.print();
      setTimeout(function() {
        printWindow.close();
      }, 500);
    } else {
      printWindow.print();
      printWindow.close();
    }
  }, true);
}



window.addEventListener('DOMContentLoaded', event => {

    $('.toggle_tooltip').tooltip({
        placement: 'top',
        delay: {
            show: 500,
            hide: 100
        }
    });

    initSelect2();

});