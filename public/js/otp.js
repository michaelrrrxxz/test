$(document).ready(function() {
    const $inputs = $('.otp-input');

    $inputs.each(function(index) {
        const $input = $(this);

        $input.on('input', function() {
            if ($input.val().length === 1 && index < $inputs.length - 1) {
                $inputs.eq(index + 1).focus(); // Move to the next input
            } else if ($input.val().length === 1 && index === $inputs.length - 1) {
                $input.blur(); // Lose focus on the last input
            }
        });

        $input.on('keydown', function(event) {
            if (event.key === 'Backspace' && $input.val().length === 0 && index > 0) {
                $inputs.eq(index - 1).focus(); // Move to the previous input on backspace
            }
        });
    });
});
