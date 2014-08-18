$jq=jQuery.noConflict();
 // Check setLocation is add product to cart
	// issend Variable check request on send
	var catalogsend		= false;
	var catalogBaseurl	= '';
	var catalogSkinurl	= '';
	var displaytotal	= '';
	var totalproduct	= '';
	var totalpager		= '';
	var ocurrentpage	= '';
	var currentpage		= '';
	var currenturl		= '';
	var typedisplay		= '';
	var totalautopage	= '';
	var stopscroll		= false;
    var ishomepage      = false;
    var catid           = '';

	var ajaxcatalog	=	function(){
		function ReplaceAll(Source,stringToFind,stringToReplace){
		  var temp = Source;
		    var index = temp.indexOf(stringToFind);
			while(index != -1){
			    temp = temp.replace(stringToFind,stringToReplace);
			    index = temp.indexOf(stringToFind);
			}
			return temp;
		}
		function showloading(){
			img	=	"<div id='ajaxcatalogsending' style='width:100%;height:68px;text-align:center;'><img style='display:inline;' src='"+catalogSkinurl+"frontend/default/default/images/opc-ajax-loader.gif'/></div>";
			$jq(".toolbar-bottom").before(img);
		}
		function showButton(){
			buttonLoad	=	"<div id='ajaxcatalogsending' style='width:100%;height:68px;text-align:center;'><button onclick='ajaxcatalog.checkSend(displaytotal,totalproduct,currentpage,totalpager);' class='button'><span><span>Load more</span></span></button></div>";
			$jq(".toolbar-bottom").before(buttonLoad);
		}
		function checkSend(a,b,c,d){

			if(a<b){

				if(c<d){
					if(catalogsend==false){
						catalogsend=true;
						page	=	c+1;
						ajaxcatalog.onSend(currenturl,'GET',page);
					}

				}
			}
		}
		return {
			checkSend:function(a,b,c,d){
				checkSend(a,b,c,d);
			},
			onReady:function(){

				$jq(window).scroll(function (){
					if(stopscroll==false){
						scrolltopctl	=	$jq(window).scrollTop();
						bottomscrollctl		=	$jq(document).height()-$jq(window).height();
						footerheightctl		=	$jq(".footer-container").outerHeight();
						bodymainempty		=	$jq('.main-container').outerHeight()-$jq('.col-main').outerHeight();
						stopscrollbtnstopctl	=	bottomscrollctl-footerheightctl-bodymainempty-150;
						if(scrolltopctl>stopscrollbtnstopctl){
								//_________Check is sending ajax request____________
								//alert(currentpage+"--"+	totalpager);
								checkSend(displaytotal,totalproduct,currentpage,totalpager);
						}
					}
				});


			},//End onReady
			onSend:function(url,typemethod,page){

				if(url.indexOf('ajaxcatalog')>0){
					param	=	{p:page};
				}else{
					param	=	{ajaxcatalog:1,p:page};
				}
                if(ishomepage==true){
                    url =   catalogBaseurl+'index.php/ajaxcatalog/';
                    param	=	{ajaxcatalog:1,p:page,category:catid,limit:displaytotal};
                }
				new Ajax.Request(url,
					{parameters:param,
					method:typemethod,
					onLoading:function(cp){
						$jq("#ajaxcatalogsending").remove();
						showloading();
					},
					onComplete:function(cp){
						catalogsend=false;
						if(200!=cp.status){
							return false;
						}else{
							//_________ Get success	_________
							list	=	cp.responseJSON;
							if(typedisplay=='grid'){
								$jq("#ajaxcatalogsending").prev().prev().removeClass('last');
                                $jq("#ajaxcatalogsending").prev().removeClass('last');
								$jq("ul.products-grid").append(list.cataloglistproduct);
								$$("#ajaxcatalogsending").invoke("replace","");
							}else{
								$jq("#ajaxcatalogsending").prev().prev().children('li').removeClass('last');
								$jq("#ajaxcatalogsending").prev().prev().append(list.cataloglistproduct);
								$$("#ajaxcatalogsending").invoke("replace",'');
							}

							if(list.toolbar!=""){
								toolbarhtml			= list.toolbar;
								$$(".toolbar").invoke("replace",list.toolbar);
							}
							showloadmore	=	false;

							if(displaytotal<totalproduct){
								if(currentpage<totalpager&&totalautopage<=currentpage){
									showloadmore	=	true;
									showButton();
									stopscroll=true;
								}
							}
							if(showloadmore==false){
								ajaxcatalog.onReady();
							}

						}

					}

				});
			}//End onSend
		}
	}();
Prototype.Browser.IE?Event.observe(window,"load",function(){ajaxcatalog.onReady()}):document.observe("dom:loaded",function(){ajaxcatalog.onReady()});
