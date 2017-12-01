/**
 * Created by arkadiy on 06.05.15.
 */
jQuery(function() {
    jQuery( "#workingButtons,#notWorkingButtons" ).sortable({
        connectWith: "#workingButtons,#notWorkingButtons",
        update: function(event, ui) {
            storeordering();
        }
    });
});

function storeordering()
{
    jQuery('#workingButtons').find('input').removeAttr('disabled');
    jQuery('#notWorkingButtons').find('input').attr('disabled', 'disabled');
}