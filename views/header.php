<?php
// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
?>
<div class="rafflys-masthead">
    <div class="rafflys-masthead__inside-container">
        <div class="rafflys-masthead__logo-container">
            <img class="rafflys-masthead__logo" width="150" src="<?php echo esc_url( plugins_url('../img/logo_rafflys_by.svg', __FILE__) ); ?>" alt="rafflys logo">
        </div>
    </div>
</div>