<?php

if ( ! defined('ABSPATH')) exit;  // if direct access


class dcCouponsImporter
{
  /**
  * Class constructor
  */
  function __construct(){
    //require_once dirname( __FILE__ ) . '/assets/classes/DataSource.php';
  }

  var $log = array();

  /**
     * Determine value of option $name from database, $default value or $params,
     * save it to the db if needed and return it.
     *
     * @param string $name
     * @param mixed  $default
     * @param array  $params
     * @return string
  */
  function process_option($name, $default, $params){
    if (array_key_exists($name, $params)) {
            $value = stripslashes($params[$name]);
        } elseif (array_key_exists('_'.$name, $params)) {
            // unchecked checkbox value
            $value = stripslashes($params['_'.$name]);
        } else {
            $value = null;
        }
        $stored_value = get_option($name);
        if ($value == null) {
            if ($stored_value === false) {
                if (is_callable($default) &&
                    method_exists($default[0], $default[1])) {
                    $value = call_user_func($default);
                } else {
                    $value = $default;
                }
                add_option($name, $value);
            } else {
                $value = $stored_value;
            }
        } else {
            if ($stored_value === false) {
                add_option($name, $value);
            } elseif ($stored_value != $value) {
                update_option($name, $value);
            }
        }
        return $value;
  }

  function getCampaignField(){
    $list = _dc_campaign_list();
    print '<select name="compaign">';
    foreach ($list as $key => $name) {
      print '<option value="'.$key.'">'.$name.'</option>';
    }
    print '</select>';
    print '<p><a href="/wp-admin/edit-tags.php?taxonomy=dc_campaign&post_type=dc_coupon">Compaign list</a></p>';
  }

  /**
   * Plugin's interface
   *
   * @return void
   */
  function form(){
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        $this->post();
    }
    // form HTML {{{
?>

<div class="wrap">
    <h2>Import CSV</h2>
    <form class="add:the-list: validate" method="post" enctype="multipart/form-data">
        <!-- Import as draft -->

        <!-- File input -->
        <p><label for="csv_import">Upload file:</label><br/>
            <input name="csv_import" id="csv_import" type="file" value="" aria-required="true" /></p>
        <p><label for="csv_import">Compaign:</label><br/>
            <?php $this->getCampaignField(); ?>
        </p>
        <p class="submit"><input type="submit" class="button" name="submit" value="Import" /></p>
    </form>
</div><!-- end wrap -->

<?php
        // end form HTML }}}

    }

    function print_messages(){
      if (!empty($this->log)) {
        // messages HTML {{{?>
<div class="wrap">
    <?php if (!empty($this->log['error'])) : ?>

    <div class="error">
        <?php foreach ($this->log['error'] as $error) : ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>

    <?php if (!empty($this->log['notice'])) : ?>

    <div class="updated fade">
        <?php foreach ($this->log['notice'] as $notice) : ?>
            <p><?php echo $notice; ?></p>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div><!-- end wrap -->
<?php  // end messages HTML }}}
$this->log = array();
        }
    }

    /**
     * Handle POST submission
     *
     * @param array $options
     * @return void
     */
    function post($options = array()){
      if (empty($_FILES['csv_import']['tmp_name'])) {
            $this->log['error'][] = 'No file uploaded, aborting.';
            $this->print_messages();
            return;
      }
      if (!current_user_can('publish_pages') || !current_user_can('publish_posts')) {
            $this->log['error'][] = 'You don\'t have the permissions to publish posts and pages. Please contact the blog\'s administrator.';
            $this->print_messages();
            return;
      }

      $skipped = 0;
      $imported = 0;
      $comments = 0;

      $term_id = $_POST['compaign'];

      $term_meta = get_term_meta($term_id);
      $term = get_term($term_id);
      $row = 0;
      $time_start = microtime(true);
      $file = $_FILES['csv_import']['tmp_name'];
      if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          /*
            data[1] = dealershipId
            data[2] = coupon headline
            data[3] = coupon text
            data[4] = is coupon active
          */

          if(dc_create_coupon_dealer($term, $term_meta, $data[1], $data[2], $data[3], $data[4])){
            $imported++;
          }
          $row++;
        }
        fclose($handle);
      }

      $exec_time = microtime(true) - $time_start;
      if (file_exists($file)) {
          @unlink($file);
      }
      if ($skipped) {
          $this->log['notice'][] = "<b>Skipped {$skipped} coupons (most likely due to empty title, body and excerpt).</b>";
      }
      $this->log['notice'][] = sprintf("<b>Imported {$imported} from {$row} coupons in %.2f seconds.</b>", $exec_time);
      $this->print_messages();
    }

}


function dealer_csv_admin_menu1()
{
    //require_once ABSPATH . '/wp-admin/admin.php';
    $plugin = new dcCouponsImporter;
    add_submenu_page('edit.php?post_type=dc_coupon', 'Import coupons (CSV)', 'Import CSV', 'manage_options', 'dc-import-csv', array($plugin, 'form'));
}
add_action('admin_menu', 'dealer_csv_admin_menu1');
