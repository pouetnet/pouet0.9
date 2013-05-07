// this piece of code more or less inspired by the livejournal code (thanks)
function preview(form,type) {
  var oldaction=form.action;
  form.action='preview_'+type+'.php';
  form.target='preview';
  var w = window.open('','preview','width=600,height=400,resizable=yes,status=yes,toolbar=no,location=no,menubar=no,scrollbars=yes');
  form.submit();
  form.action=oldaction;
  form.target='_self';
  return false;
} 
function popupGroupSelector(form,field) {
  window.open("popup_groups.php?form="+form+"&field="+field, "selectgroup", "width=450,height=400,scrollbars=yes,statusbar=no,menubar=no,resizable=yes");
}
function popupPartySelector(form,field) {
  window.open("popup_parties.php?form="+form+"&field="+field, "selectparty", "width=450,height=400,scrollbars=yes,statusbar=no,menubar=no,resizable=yes");
}
function popupAvatarSelector(form,field) {
  window.open("popup_avatars.php?form="+form+"&field="+field, "selectavatar", "width=450,height=400,scrollbars=yes,statusbar=no,menubar=no,resizable=yes");
}
function popupProdLastYearSelector(form,field) {
  window.open("popup_prodslastyear.php?form="+form+"&field="+field, "selectprodlastyear", "width=450,height=400,scrollbars=yes,statusbar=no,menubar=no,resizable=yes");
}
