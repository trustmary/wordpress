const trustmary_settings_radios = document.querySelectorAll('.toggle-script-block');
const trustmary_script_block = document.getElementById('trustmary-script');

function trustmary_toggle_script_block(event) {
    if (parseInt(this.value) === 1)
        trustmary_script_block.style.display = 'none';
    else
        trustmary_script_block.style.display = 'block';
}

trustmary_settings_radios.forEach((el) => {
    el.addEventListener('change', trustmary_toggle_script_block);
});

function trustmary_shortcode_copy_clipboard(e) {
    e.preventDefault();
    e.target.select();
    const str = e.target.value;
    if (navigator && navigator.clipboard && navigator.clipboard.writeText)
        return navigator.clipboard.writeText(str);
    return Promise.reject('The Clipboard API is not available.');
};

const trustmary_shortcode_fields = document.querySelectorAll('.copy-shortcode');

trustmary_shortcode_fields.forEach((el) => {
    el.addEventListener('click', trustmary_shortcode_copy_clipboard);
});