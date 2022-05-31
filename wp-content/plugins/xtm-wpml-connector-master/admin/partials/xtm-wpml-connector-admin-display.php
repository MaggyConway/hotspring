<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://xtm-intl.com/
 * @since      1.0.0
 *
 * @package    Xtm_Wpml_Connector
 * @subpackage Xtm_Wpml_Connector/admin/partials
 */

?>
<?php global $sitepress ?>
<?php
$bridge = new Xtm_Wpml_Bridge();
$authorization = $bridge->find_customer();
$options = get_option($this->plugin_name);

Xtm_Wpml_Connector_Helper::display_notices($options, $authorization);
?>

<div class="wrap <?php echo $this->plugin_name ?>-block">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <form method="post" name="xtm_options" action="options.php">
        <?php

        $url = $options[Xtm_Wpml_Bridge::XTM_API_URL];
        $name = $options[Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME];
        $user_id = $options[Xtm_Wpml_Bridge::XTM_API_USER_ID];
        $customer_id = $options[Xtm_Wpml_Bridge::XTM_PROJECT_CUSTOMER_ID];
        $password = $options[Xtm_Wpml_Bridge::XTM_API_PASSWORD];
        $prefix = $options[Xtm_Wpml_Bridge::PROJECT_NAME_PREFIX];
        $translator_email = $options[Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL];
        $automatically_move_flag = $options[Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG];
        $automatically_first_move_flag = $options[Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG];
        $template_id = $options[Xtm_Wpml_Bridge::API_TEMPLATE_ID];
        ?>
        <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
        ?>
        <fieldset>
            <legend class="screen-reader-text"><span>XTM API URL</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_URL ?>">
                <span><?php esc_attr_e('XTM API URL', $this->plugin_name); ?></span>
                <input class="regular-text" type="text"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_URL ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_API_URL ?>]"
                       value="<?php echo $url ?>"/>
            </label>
        </fieldset>
        <fieldset>
            <legend class="screen-reader-text"><span>XTM API Client name</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME ?>">
                <span><?php esc_attr_e('XTM API Client name', $this->plugin_name); ?></span>
                <input class="regular-text" type="text"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_API_CLIENT_NAME ?>]"
                       value="<?php echo $name ?>"/>
            </label>
        </fieldset>
        <fieldset>
            <legend class="screen-reader-text"><span></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_USER_ID ?>">
                <span><?php esc_attr_e('XTM API User ID', $this->plugin_name); ?></span>
                <input class="regular-text" type="text"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_USER_ID ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_API_USER_ID ?>]"
                       value="<?php echo $user_id ?>"/>
            </label>
        </fieldset>
        <fieldset>
            <legend class="screen-reader-text"><span></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_PASSWORD ?>">
                <span><?php esc_attr_e('XTM API Password', $this->plugin_name); ?></span>
                <input class="regular-text" type="password"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_API_PASSWORD ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_API_PASSWORD ?>]"
                       value="<?php echo $password ?>"/>
            </label>
        </fieldset>
        <fieldset>
            <legend class="screen-reader-text"><span></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_PROJECT_CUSTOMER_ID ?>">
                <span><?php esc_attr_e('XTM project Customer ID', $this->plugin_name); ?></span>
                <input class="regular-text" type="text"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_PROJECT_CUSTOMER_ID ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_PROJECT_CUSTOMER_ID ?>]"
                       value="<?php echo $customer_id ?>"/>
            </label>
        </fieldset>
        <fieldset>
            <legend class="screen-reader-text"><span></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::PROJECT_NAME_PREFIX ?>">
                <span><?php esc_attr_e('Project name prefix', $this->plugin_name); ?></span>
                <input class="regular-text" type="text"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::PROJECT_NAME_PREFIX ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::PROJECT_NAME_PREFIX ?>]"
                       value="<?php echo $prefix ?>"/>
            </label>
        </fieldset>
        <fieldset>
            <legend class="screen-reader-text"><span></span></legend>
            <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL ?>">
                <span><?php esc_attr_e('Email user response for XTM translation', $this->plugin_name); ?></span>
                <input class="regular-text" type="text"
                       id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL ?>"
                       name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_TRANSLATOR_EMAIL ?>]"
                       value="<?php echo $translator_email ?>"/>
            </label>
        </fieldset>
        <?php include 'xtm-wmpl-connector-admin-language-mappings.php'?>
        <?php if ($authorization) : ?>
            <h2><?php esc_attr_e('Default configuration', 'wp_admin_style'); ?></h2>
            <fieldset>
                <legend class="screen-reader-text"><span></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG ?>">
                    <input name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG ?>]"
                           type="checkbox"
                        <?php if ($automatically_move_flag): ?> checked <?php endif; ?>
                           id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_AUTOMATICALLY_MOVE_FLAG ?>"
                           value="1"/>
                    <span class="xtm-radio">
                        <?php esc_attr_e('Automatically move jobs from xtm translator to XTM System with default settings.',
                            'wp_admin_style'); ?>
                    </span>
                </label>
            </fieldset>
            <div id="automatically" <?php if (!$automatically_move_flag) {
                echo "style='display:none'";
            } ?>>
                <fieldset>
                    <legend class="screen-reader-text"><span></span></legend>
                    <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG ?>">
                        <input name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG ?>]"
                               type="checkbox"
                            <?php if ($automatically_first_move_flag): ?> checked <?php endif; ?>
                               id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::XTM_FIRST_AVAILABLE_AUTOMATICALLY_MOVE_FLAG ?>"
                               value="1"/>
                        <span class="xtm-radio">
                        <?php esc_attr_e('Automatically send to XTM files from translator: "First available (Local)" ',
                            'wp_admin_style'); ?>
                    </span>
                    </label>
                </fieldset>
                <fieldset>
                    <legend class="screen-reader-text"><span></span></legend>
                    <label for="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::API_TEMPLATE_ID ?>">
                        <span><?php esc_attr_e('Default template Id', $this->plugin_name); ?></span>
                        <select id="<?php echo $this->plugin_name; ?>-<?php echo Xtm_Wpml_Bridge::API_TEMPLATE_ID ?>"
                                name="<?php echo $this->plugin_name; ?>[<?php echo Xtm_Wpml_Bridge::API_TEMPLATE_ID ?>]">
                            <option value=""><?php _e("No template", Xtm_Wpml_Bridge::PLUGIN_NAME); ?></option>
                            <?php foreach ($bridge->get_templates() as $key => $template) : ?>
                                <option value="<?php echo $key ?>" <?php if ($key == $template_id) : ?> selected="selected"<?php endif; ?>>
                                    <?php echo $template ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </fieldset>
<!--                <fieldset>
                    <h4><?php /*esc_attr_e('Project modes', 'wp_admin_style'); */?></h4>
                    <?php /*foreach ($helper->get_project_modes() as $value => $projectMode) : */?>
                        <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                        <label title='g:i a'>
                            <input type="radio"
                                   name="<?php /*echo $this->plugin_name; */?>[<?php /*echo Xtm_Wpml_Bridge::API_PROJECT_MODE */?>]"
                                   value="<?php /*echo (int)$value */?>"
                                <?php /*if ($value == $options[Xtm_Wpml_Bridge::API_PROJECT_MODE]) : */?>
                                    checked="checked"
                                <?php /*endif; */?>
                            />
                            <span class="xtm-radio"><?php /*esc_attr_e(esc_html($projectMode),
                                    'wp_admin_style'); */?></span>
                        </label>
                        <br/>
                    <?php /*endforeach; */?>
                </fieldset>-->
            </div>
        <?php endif; ?>
        <?php submit_button('Save all changes', 'primary', 'submit', true); ?>
    </form>
</div>
