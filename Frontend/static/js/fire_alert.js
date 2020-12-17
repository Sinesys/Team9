function fireAlertError(message, action) {
    Swal.fire({
            showConfirmButton: true,
            allowOutsideClick: false,
            icon: 'error',
            title: 'Attention!',
            text: message
        })
        .then((result) => {
            if (result.isConfirmed) {
                if (action != undefined)
                    action();
            }
        })
}

function fireAlertSuccess(message, action) {
    Swal.fire({
            showConfirmButton: true,
            allowOutsideClick: false,
            icon: 'success',
            title: 'Success!',
            text: message
        })
        .then((result) => {
            if (result.isConfirmed) {
                if (action != undefined)
                    action();
            }
        })
}

function fireConfirm(message, action) {
    Swal.fire({
            title: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        })
        .then((result) => {
            if (result.value)
                action();
        })
}