<?php
global $sitepress;
$active_languages = $sitepress->get_active_languages();

$options = get_option($this->plugin_name);
$helper = new Xtm_Wpml_Connector_Helper();
$xtm_language = $helper->get_xtm_languages();

?>
<fieldset>
    <h2><?php
        esc_attr_e('Language mappings', 'wp_admin_style'); ?></h2>
    <?php
    foreach ($active_languages as $active_language) :
        $code = $active_language['code'];
        $xtm_lang_code = $xtm_language[$code];
        ?>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo $code ?>">
                <span>
                    <?php esc_attr_e($active_language['display_name'], $this->plugin_name); ?>
                    (<?php echo $code ?>)
                </span>
            </label>
            <?php
            if (!isset($xtm_lang_code)) :
                esc_attr_e('We can not find language mappings for this language', $this->plugin_name);
            else : ?>
                <select name="<?php echo $this->plugin_name; ?>[lang-<?php echo $code; ?>]"
                        id="<?php echo $this->plugin_name; ?>-<?php echo $code ?>">
                    <?php  if (count($xtm_lang_code) > 1) : ?>
                        <?php foreach ($xtm_lang_code as $lang) : ?>
                            <option <?php
                            if ($options['remote_languages_mappings'][$code] == array_keys($lang)[0]
                            ) : ?>
                                selected="selected" <?php endif; ?>
                                    value="<?php echo array_keys($lang)[0] ?>">
                                <?php echo array_values($lang)[0] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <option value="<?php echo array_keys($xtm_lang_code)[0] ?>">
                            <?php echo array_values($xtm_lang_code)[0] ?>
                        </option>
                    <?php endif; ?>
                </select>
            <?php  endif; ?>
        </fieldset>
        <br/>
    <?php
    endforeach; ?>
</fieldset>
