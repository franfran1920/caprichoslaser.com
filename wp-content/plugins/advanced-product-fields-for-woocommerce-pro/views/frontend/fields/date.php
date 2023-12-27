<?php
/** @var array $model */
use \SW_WAPF_PRO\Includes\Classes\Helper;

$field_id = esc_attr($model['field']->id);
$disable_past = isset($model['field']->options['disable_past']) && $model['field']->options['disable_past'];
$disable_future = isset($model['field']->options['disable_future']) && $model['field']->options['disable_future'];
$disable_today = isset($model['field']->options['disable_today']) && $model['field']->options['disable_today'];
$today_server = DateTime::createFromFormat('D M d Y H:i:s O',current_time('D M d Y H:i:s O'))->setTime(0,0,0);
$date_format = esc_attr(get_option('wapf_date_format','mm-dd-yyyy'));
$pattern = Helper::date_format_to_regex($date_format);
$offset = $today_server->getOffset();

$default_date_string = false;

if($model['is_edit']) {
    $default_date_string = $model['default'][0];
} else if(!empty($model['field']->options['default'])) {

$default_date = Helper::string_to_date( $model['field']->options['default'] );

    $default_date_string = $default_date->format(Helper::date_format_to_php_format( $date_format ));
}
?>
<input inputmode="none" data-format="<?php echo $date_format;?>" value="<?php echo $default_date_string ? $default_date_string : ''?>" autocomplete="off" pattern="<?php echo $pattern;?>" type="text" <?php echo $model['field_attributes']; ?> />
<script>
    jQuery(function($) {
        window.initWapfDate = window.initWapfDate || [];
        if(!window.initWapfDate['<?php echo $field_id; ?>']) {
            window.initWapfDate['<?php echo $field_id; ?>'] = function (field) {
                var offset = <?php echo $offset;?>;
                var today = new Date(wapf_config.today);
                var $this = typeof field  === 'string' ? $('.input-' + field ) : field;

                $this.dp({
                    autoHide: true,
                    weekStart: <?php echo get_option('start_of_week', '0');?>,
                    format: '<?php echo $date_format;?>',
                    months:  <?php echo json_encode( $model['data']['months'] );?>,
                    monthsShort:  <?php echo json_encode( $model['data']['monthsShort'] );?>,
                    days:  <?php echo json_encode( $model['data']['days'] );?>,
                    daysMin:  <?php echo json_encode( $model['data']['daysShort'] );?>,
                    filter: function(date, view) {
                        if(view === 'day') {
                            var isToday = date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
                            <?php if($disable_today) { ?>
                            if(isToday) return false;
                            <?php } ?>
							<?php if($disable_future) { ?>
                            if(date > today) return false;
							<?php } ?>
							<?php if($disable_past) { ?>
                            if(date < today) return false;
							<?php } ?>
                            return WAPF.Filter.apply('date/selectable', true, { date: date, offset: offset,field:$this, today:today,isToday:isToday } );
                        }
                    },
                    pick: function() {
                        document.dispatchEvent( new CustomEvent('wapf/date_selected', { detail: { date: $this.data('selected'), input: $this[0] } }) );
                    }
                });
            };
        }
        window.initWapfDate['<?php echo $field_id; ?>']('<?php echo $field_id; ?>');
        $(document).on('wapf/cloned', function(e,fieldId,idx,$clone) {
            var isSection = $('.field-'+fieldId).hasClass('wapf-section');
            if(!isSection && fieldId !== '<?php echo $field_id;?>') return;
            var $f = $clone.find((isSection ? '.field-<?php echo $field_id;?> ' : '')+'.wapf-input');
            $f.val('').data('selected',null).off('focus').data('wapf-dp',null);
            window.initWapfDate['<?php echo $field_id;?>']($f);
        });
    });
</script>