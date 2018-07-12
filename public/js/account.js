jQuery(document).ready(function() {
    if(jQuery('#lookup_account_form').length > 0)
    {
        const accountNameInput = jQuery('#lookup_account_form #account_name');
        accountNameInput.on('change', function() {
            const accountName = accountNameInput.val();
            lookupAccounts(accountName);
        });
        jQuery(accountNameInput).keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              const accountName = accountNameInput.val();
              lookupAccounts(accountName);
              return false;
            }
        });
        jQuery(document).on('submit','lookup_account_form',function(e){
            e.preventDefault();
            const accountName = accountNameInput.val();
            lookupAccounts(accountName);
            return false;
        });
    }
});

function lookupAccounts(accountName)
{
    jQuery.post('/account/lookup', {'accountName' : accountName}, function(response) {
        jQuery('#accountsTableBody').empty();
        jQuery.each(response, function(id, name) {
            jQuery('#accountsTableBody').append('<tr><td><a href="/account/id/' + id + '">' + id + '</a></td><td><a href="/account/name/' + name + '">' + name + '</a></td></tr>');
        });
    }, 'json');
}
