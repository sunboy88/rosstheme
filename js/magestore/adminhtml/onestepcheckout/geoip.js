
function getLinkImport(link)
{					
	var parameters = countryGrid_massactionJsObject.getCheckedValues();	
	if(!parameters){
		alert('Please select country.');
		return false;
	}
	popWin(link+'id/'+parameters,'import','top:0,left:0,width=700,height=400,resizable=yes,scrollbars=yes');
}	

function importGeoIp(link)
{
	if(link){
		popWin(link,'import','top:0,left:0,width=700,height=400,resizable=yes,scrollbars=yes');
	}
	else
		alert('GeoIP database was updated!');
		return;
}