/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_AjaxCart
 * @copyright   Copyright (c) 2014 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

;_plAjaxCart = function(conf){
	var $this = this;

	$this.items = {};
	$this.itemsArh = {};
	$this.itemsImage = {};
	//$this.cssJs = {}
	$this.removedAttrs = [];
	$this.onReady = {};
	$this.observers = {};

	$this._data = {};

	$this.conf = {
		minicartHolder : '#header-minicart',
		unexpectedError : 'Unexpected Error. Please contact us.',
		isCartPage : false,
		isCheckoutPage : false,
		micartTemplate : 0,
		mincartIsClosed : true,
		afterAddShow : 0
	}

	$this.ajaxIsBlocked = false;

	for (var i in conf) {
		$this.conf[i] = conf[i];
	}

	$this.decQty = function(id) {
		if (!$this.items[id]) return;
		$this.dispatchEvent('before_decQty',{id:id});
		$this.undateQty(id, parseInt($this.items[id].qty) - 1);
	}

	$this.incQty = function(id) {
		if (!$this.items[id]) return;
		$this.dispatchEvent('before_incQty',{id:id});
		$this.undateQty(id, parseInt($this.items[id].qty) + 1);
	}

	$this.undateQty = function(id, qty) {
		if (!$this.items[id] || $this.ajaxIsBlocked) return;
		if (qty <= 0) {
			qty = 0;
		}
		pjQuery_1_10_2('[name="cart['+id+'][qty]"]').val(qty);
		$this.items[id].qty = qty;

		if (typeof ajaxCartUndateQtyInterval != 'undefined') {
			clearTimeout(ajaxCartUndateQtyInterval);
		}

		ajaxCartUndateQtyInterval = setTimeout(function(){
			$this.dispatchEvent('before_undateQty',{id:id,qty:qty});
			if (qty <= 0) {
				$this.deleteFromCart(id);
				return;
			} else {
				var req = $this.getPreparedData({id:id,qty:qty});
				pjQuery_1_10_2.ajax({
					url 	: $this.conf.updateQtytUrl,
					data 	: req,
					dataType : 'json',
					beforeSend : function(xhr, opts){
				        $this.beforeAjaxSend($this.items[id].product_id);
				    },
					success : function(res) {
						$this.processSuccessResponse(res, req);
					},
					error : function() {
						$this.processErrorResponse(req);
					}
				});
			}
		}, 700);
	}

	$this.configure = function(id) {
		if (!$this.items[id] || $this.ajaxIsBlocked) return;
		$this.dispatchEvent('before_configure',{id:id});
		var req = $this.getPreparedData({id:id});
		pjQuery_1_10_2.ajax({
			url 	: $this.conf.configureUrl,
			data 	: req,
			dataType : 'json',
			beforeSend : function(xhr, opts){
				$this.beforeAjaxSend($this.items[id].product_id);
			},
			success : function(res) {
				$this.processSuccessResponse(res, req);
			},
			error : function() {
				$this.processErrorResponse(req);
			}
		});	
	}

	$this.deleteFromCart = function(id) {
		if (!$this.items[id] || $this.ajaxIsBlocked) return;
		$this.dispatchEvent('before_deleteFromCart',{id:id});
		var itemHld = pjQuery_1_10_2('.ajaxcart-item-'+id).hide();
		var req = $this.getPreparedData({id:id});
		pjQuery_1_10_2.ajax({
			url 	: $this.conf.deleteFromCartUrl,
			data 	: req,
			dataType : 'json',
			beforeSend : function(xhr, opts){
				$this.beforeAjaxSend($this.items[id].product_id);
			},
			success : function(res) {
				if (res.success) {
					delete($this.items[id]);
					itemHld.remove();
					if (!res.qty && $this.conf.isCartPage) {
						location.reload();
					}
				} else {
					itemHld.show();
				}
				$this.processSuccessResponse(res, req);

			},
			error : function() {
				itemHld.show();
				$this.processErrorResponse(req);
			}
		});
	}

	$this.addProductById = function(id, info) {
		var data = {product:id};
		var qty = parseInt(pjQuery_1_10_2('[name=pac_btn_cart_qty_'+id+']').val());
		if (qty > 1) {
			data.qty = qty;
		}
		if (info) data = pjQuery_1_10_2.extend(data, info);
		$this.addToCart(data);
	}

	$this.addProductByForm = function(form, origPid) {
		$this.dispatchEvent('before_addProductByForm', form);

		var validator  = new Validation(form);
		if (validator && validator.validate()) {
			var d = $this.getFormData(form);
			if (origPid) {
				d['orig_product'] = origPid;
			}
			d['isFormSubmit'] = true;
			$this.addToCart(d);
		} else {
			pjQuery_1_10_2.fancybox.update();
		}
	}

	$this.addToCart = function(data) {

		if ($this.ajaxIsBlocked) return;
		$this.dispatchEvent('before_addToCart',{'data':data});
		var req = $this.getPreparedData(data);
		pjQuery_1_10_2.ajax({
			url 	: $this.conf.addToCartUrl,
			data 	: req,
			dataType : 'json',
			beforeSend : function(xhr, opts){
				$this.beforeAjaxSend(data['product'], data);
			},
			success : function(res) {

				// delete res.html.minicart_head;
				// console.log(res);

				if (res.messages){
					$this.hideAjaxPopup();
				}
				$this.processSuccessResponse(res, req);

				$this.dispatchEvent('after_addToCart',{'res':res,'req':req});

				var error = (res.messages && res.messages['error']) ? true : false;
				if (!error && $this.conf.afterAddShow == 3 && !res.hideMinicart) {
					$this.showMinicart();
				}
			},
			error : function() {
				$this.processErrorResponse(req);
			}
		});
	}

	$this.addToCartFromWishlist = function(itemId) {
		if ($this.ajaxIsBlocked) return;
		var req = $this.getPreparedData({item : itemId});

		if (pjQuery_1_10_2('input[name="qty[' + itemId + ']"]').length) {
			req.qty = pjQuery_1_10_2('input[name="qty[' + itemId + ']"]').val();
		}

		pjQuery_1_10_2.ajax({
			url 	: $this.conf.addToCartFromWishlistUrl,
			data 	: req,
			dataType : 'json',
			beforeSend : function(xhr, opts){
				$this.beforeAjaxSend($this.itemsArh[itemId]['product_id']);
			},
			success : function(res) {
				if (res.messages){
					$this.hideAjaxPopup();
				}
				$this.processSuccessResponse(res, req);
			},
			error : function() {
				$this.processErrorResponse(req);
			}
		});
	}

	$this.getFormData = function(form) {
		var d = {};
			pjQuery_1_10_2.each(pjQuery_1_10_2(form).serializeArray(), function(i, field) {
				if (typeof d[field.name] == 'undefined') {
					d[field.name] = field.value;
				} else {
					if (typeof d[field.name] == 'string') {
						d[field.name] = [d[field.name]];
					}
					d[field.name].push(field.value);
				}

		});
		return d;
	}

	$this.updateItemByForm = function(form) {
		var validator  = new Validation(form);
		if (!$this.ajaxIsBlocked && validator && validator.validate()) {
			var s = pjQuery_1_10_2(form).serialize();
			var data = $this.getFormData(form);

			data['isFormSubmit'] = true;
			$this.dispatchEvent('before_updateItemByForm',{'data':data});
			var req = $this.getPreparedData(data);
			pjQuery_1_10_2.ajax({
				url 	: $this.conf.updateItemUrl,
				data 	: req,
				dataType : 'json',
				beforeSend : function(xhr, opts){
					$this.beforeAjaxSend(data['product'], data);
				},
				success : function(res) {
					if (res.messages){
						$this.hideAjaxPopup();
					}
					$this.processSuccessResponse(res, req);
					if ($this.conf.isConfigurePage) {
						window.location = $this.conf.cartUrl;
					}
				},
				error : function() {
					$this.processErrorResponse(req);
				}
			});
		}
	}


	var _getWislistReqData = function(form) {
		var data = $this.getFormData(form);
		data['isFormSubmit'] = true;

		var req = $this.getPreparedData(data);

		if (!req['item']) {
			var fa =pjQuery_1_10_2(form).attr('action');
			var re = /(\w+)\/wishlist\/index\/cart\/item\/(\d+)\/(\w*)/;
			var item = parseInt(fa.replace(re, "$1|$2|$3").split('|')[1]);
			if (item) {
				req['item'] = item;
			}
		}
		return req;
	}


	$this.togglePopupDiscountForm = function(el, selector) {
		pjQuery_1_10_2('.fancybox_ajaxcart_minicart .discount-form').each(function(){
			if (pjQuery_1_10_2(selector).get(0) != pjQuery_1_10_2(this).get(0)) {
				pjQuery_1_10_2(this).hide();
			}
		});
		pjQuery_1_10_2('.fancybox_ajaxcart_minicart .discount .show-form').text('+');
		pjQuery_1_10_2(selector).toggle();
		if ( pjQuery_1_10_2(selector).is(':visible') ) {
			pjQuery_1_10_2(el).text('-');
		} else {
			pjQuery_1_10_2(el).text('+');
		}
	}


	$this.applyRewardPoints =  function(form, cancel)
	{
		$input = pjQuery_1_10_2('#pac_rewards_point_count', form);

		pjQuery_1_10_2('.pac-loader', form).show();
		var req = $this.getPreparedData({rewards_point_count : $input.val()});
		url = (cancel) ? $this.conf.deactivateRewardPointsUrl : $this.conf.applyRewardPointsUrl;
		pjQuery_1_10_2.ajax({
			url : url,
			dataType : 'json',
			data : req,
			success : function(res) {
				req.discount = true;
				$this.processSuccessResponse(res, req);
				pjQuery_1_10_2('#minicart-reward-credit-form .show-form').click();
			},
			error : function() {
				$this.processErrorResponse(req);
			}
		})
	}


	$this.applyDiscountCode = function(form, remove) {
		var validator  = new Validation(form[0]);
		if (!$this.ajaxIsBlocked && validator && validator.validate()) {			
			var $couponCode = form.find('input[name=coupon_code]');
			pjQuery_1_10_2('.pac-loader', form).show();
			var data = {
				coupon_code : $couponCode.val(),
				remove : remove
			};
			var req = $this.getPreparedData(data);
			pjQuery_1_10_2.ajax({
				url : $this.conf.applyDiscountCodeUrl,
				data : req,
				type : form.attr('method'),
				dataType : 'json',
				success : function (res) {
					req.discount = true;
					$this.processSuccessResponse(res, req);
					pjQuery_1_10_2('#minicart-discount-coupon-form .show-form').click();
				},
				error : function() {
					$this.processErrorResponse(req);
					pjQuery_1_10_2('#minicart-discount-coupon-form .show-form').click();
					pjQuery_1_10_2('.pac-loader', form).hide();
				}
			})
		}
	}


	$this.addWishlistItemByForm = function(form) {
		var validator  = new Validation(form);
		if (!$this.ajaxIsBlocked && validator && validator.validate()) {

			var req = _getWislistReqData(form);
			req['removefromwishlist'] = 1;

			pjQuery_1_10_2.ajax({
				url 	: $this.conf.addToCartFromWishlistUrl,
				data 	: req,
				dataType : 'json',
				beforeSend : function(xhr, opts){
					$this.beforeAjaxSend(req['product']);
				},
				success : function(res) {
					if (res.messages){
						$this.hideAjaxPopup();
					}
					$this.processSuccessResponse(res, req);
					/*if ($this.conf.isConfigurePage) {
						window.location = $this.conf.cartUrl;
					}*/
				},
				error : function() {
					$this.processErrorResponse(req);
				}
			});
		}
	}

	$this.updateWishlistItemByForm = function(form) {
		var validator  = new Validation(form);
		if (!$this.ajaxIsBlocked && validator && validator.validate()) {

			var req = _getWislistReqData(form);

			pjQuery_1_10_2.ajax({
				url 	: $this.conf.updateWishlistItemUrl,
				data 	: req,
				dataType : 'json',
				beforeSend : function(xhr, opts){
					$this.beforeAjaxSend(req['product'], req);
				},
				success : function(res) {
					if (res.messages){
						$this.hideAjaxPopup();
					}
					$this.processSuccessResponse(res, req);
					if ($this.conf.isConfigurePage) {
						window.location = $this.conf.wishlistUrl;
					}
				},
				error : function() {
					$this.processErrorResponse(req);
				}
			});
		}
	}

	$this.beforeAjaxSend = function(productId, data) {
		pjQuery_1_10_2('.ajaxcart-el').attr('disabled', 'disabled').addClass('disabled');
		pjQuery_1_10_2('.ajaxcart-el-'+productId).removeAttr('disabled').removeClass('disabled');
		pjQuery_1_10_2('.pac-btn-cart-'+productId).addClass('pac-loading');

		if (data && data['orig_product']) {
			pjQuery_1_10_2('.ajaxcart-el-'+data['orig_product']).removeAttr('disabled').removeClass('disabled');
			pjQuery_1_10_2('.pac-btn-cart-'+data['orig_product']).addClass('pac-loading');
		}

		$this.ajaxIsBlocked = true;
		$this.dispatchEvent('beforeAjaxSend',{'productId':productId, 'data' : data});
	}

	$this.afterAjaxReceiving = function(productId) {
		pjQuery_1_10_2('.ajaxcart-el').removeAttr('disabled').removeClass('disabled');
		pjQuery_1_10_2('.pac-btn-cart').removeClass('pac-loading');
		$this.ajaxIsBlocked = false;
		$this.dispatchEvent('afterAjaxReceiving',{'productId':productId});
		/*
		* Hack for joomla invalid token reload hole page
		* after resize quantity
		* SP 20161214
		* */
		/*window.location = 'http://joomla.aurednik.mfg/webshop/checkout/cart/';*/
	}

	$this.getPreparedData = function(data) {
		$this.dispatchEvent('before_getPreparedData',{'data':data});
		data['isCartPage'] = $this.conf.isCartPage;
		data['isCheckoutPage'] = $this.conf.isCheckoutPage;
		data['htmlBlocks'] = $this.conf.dinamicDefaultBlocks;
		data['categoryId'] = $this.conf.categoryId;
		data['isCategoryPage'] = $this.conf.isCategoryPage;
		//data['cssJs'] = $this.cssJs;

		if ($this.conf.isCartPage) {
			data['htmlBlocks'] = data['htmlBlocks'].concat($this.conf.dinamicCartPageBlocks)
		}
		$this.dispatchEvent('after_getPreparedData',{'data':data});
		return data;
	}

	$this._getBlockClass = function(key) {
		return key.replace(/\./g,'_')+'_holder';
	}
	$this.uppendCssJs = function(cssJs) {
	}

	$this._revertAttrs = function() {
		for (var i = 0; i < $this.removedAttrs.length; i++){
			var item = $this.removedAttrs[i];
			var e = item['element'];
			if (e) {
				if (item['id']) {
					e.attr('id', item['id']);
				}
				if (item['class']) {
					e.addClass(item['class']);
					e.removeClass(item['class']+'-rmvd');
				}
			}
		}
		$this.removedIds = [];
	}

	$this._removeAttrs = function() {
		pjQuery_1_10_2("[id^='product-price-'],[id^='old-price-'],[id^='price-excluding-tax-'],[id^='price-including-tax-'],[id^='configurable_swatch_'],[id^='attribute'],[id^='swatch'],[id^='option'],[id^='select_label_'],[id$='_label']").each(function(){
			var e = pjQuery_1_10_2(this);
			if (e.parents('#pac-popup-internal').length) return;
			$this.removedAttrs.push({
				'element' : e,
				'id' : e.attr('id')
			});
			e.removeAttr('id');
		});

		var cls = {
			'super-attribute-select':'.super-attribute-select',
			'benefit':'.tier-prices .benefit',
			'tier-price':'.tier-prices .tier-price',
			'product-image-gallery':'.product-image-gallery'
		};
		for (var cl in cls) {
			pjQuery_1_10_2(cls[cl]).each(function(){
				var e = pjQuery_1_10_2(this);
				if (e.parents('#pac-popup-internal').length) return;
				$this.removedAttrs.push({
					'element' : e,
					'class' : cl
				});
				e.removeClass(cl);
				e.addClass(cl+'-rmvd');
			});
		}
	}

	$this.processSuccessResponse = function(res, req){
		$this.dispatchEvent('before_processSuccessResponse',{'res':res,'req':req});
		$this.afterAjaxReceiving();


		var isFBox = false;
		if (res.html) {
			for(var i in res.html) {
				if (i == 'product.info') {
					$this._revertAttrs();
					$this._removeAttrs();

					/* configure swatch fix on product list*/
					var CMIOpt = ['productImages', 'imageObjects', 'imageType'];
					if (typeof ConfigurableMediaImages != 'undefined') {
						for(var z=0;z<CMIOpt.length;z++) {
							var _ok = CMIOpt[z];
							$this._data['CMI_'+_ok] = ConfigurableMediaImages[_ok];
							ConfigurableMediaImages[_ok] = {};
						}
					}
					/* end configure swatch fix on product list*/

					/* product image zoom fix on product list*/
					var pmcz = 'ProductMediaManager_init';
					if (typeof ProductMediaManager != 'undefined') {
						$this._data[pmcz] = ProductMediaManager.createZoom;
						ProductMediaManager.createZoom = function(){};
					}
					/*end product image zoom fix on product list*/

					var wW = pjQuery_1_10_2(window).width() <= 1024;
					var isA = pjQuery_1_10_2.fancybox.isActive;
					if (wW && isA) {
						pjQuery_1_10_2.fancybox.close();
					}

					// Fix for aurednik.de
					// There are conflicts between jquery and prototype
					pjQuery_1_10_2.noConflict();
					if (typeof $ !== 'undefined' && typeof $.noConflict == 'function') {
						$.noConflict();
					}

					setTimeout(function(){
						pjQuery_1_10_2.fancybox({
							content : '<div id="pac-popup-internal" class="pac-mode-2">'+res.html[i]+'</div>',
							autoResize : true,
							maxWidth : $this.conf.popupWidth,
							wrapCSS : res.fullActionName ? 'fancybox_'+res.fullActionName : 'fancybox_ajaxcart',
							openEffect : $this.isAjaxPopupOpen ? 'none' : 'fade',
							afterLoad : function(){
								$this._removeAttrs();
								$this.dispatchEvent('after_processSuccessResponseFancyboxLoad',{'res':res,'req':req});
							},
							afterClose : function() {
								$this._revertAttrs();
								/* configure swatch fix on product list*/
								for(var z=0;z<CMIOpt.length;z++) {
									var _ok = CMIOpt[z];
									if ($this._data['CMI_'+_ok]) {
										ConfigurableMediaImages[_ok] = $this._data['CMI_'+_ok];
										delete($this._data['CMI_'+_ok]);
									}
								}
								/* end configure swatch fix on product list*/

								/* product image zoom fix on product list*/
								if ($this._data[pmcz]) {
									ProductMediaManager.createZoom = $this._data[pmcz];
									ProductMediaManager.init();
									delete($this._data[pmcz]);
								}
								/* enc product image zoom fix on product list*/
								$this.dispatchEvent('after_hideAjaxPopup');
							},
							helpers     : {
						        overlay : {locked : wW}
						    }
						});
					}, wW&&isA ? 500 : 0);

					isFBox = true;
					continue;
				}

				var cl  = $this._getBlockClass(i);
				var s = '.'+cl;

				//fix for aurednik.de
				if (s == '.content_holder' && !pjQuery_1_10_2(s).length) {
					s = '.magebridge-content';
				}

				if (!pjQuery_1_10_2(s).length) {
					alert('Cannot find '+s+' DOM element.');
				}
				var html = pjQuery_1_10_2(res.html[i]);
				if (html.hasClass(cl)) {
					html = html.html();
				}

				var hld = pjQuery_1_10_2(s).html(html);
			}
		}

		if (!isFBox) {
			$this.hideAjaxPopup();
		}

		if (res.messages){
			$this.showMessages(res.messages, res, req);
		}

		var error = (res.messages && res.messages['error']) ? true : false;
		if (!error && $this.conf.afterAddShow == 3 && !res.hideMinicart && res.items_id.length) {
			$this.showMinicart();
		}

		$this.onDocumentReady();
		$this.dispatchEvent('after_processSuccessResponse',{'res':res,'req':req});


		if ($this.conf.isWishlistPage && res.items_id.length > 0) {
			var $item = pjQuery_1_10_2('#wishlist-view-form tr#item_' + req.item);
			if (pjQuery_1_10_2('#wishlist-table tbody tr').length == 1) {			
				setTimeout('location.reload()', 2000);
			} else {
				$item.remove();
			}
		}
	}


	/* Messages */
	$this.processErrorResponse = function(req) {
		$this.dispatchEvent('before_processErrorResponse',{'req':req});
		$this.afterAjaxReceiving();
		var msg = $this.conf.unexpectedError;
		$this.showMessages({'error':[msg]}, false, req);

		$this.onDocumentReady();
		$this.dispatchEvent('after_processErrorResponse',{'req':req});
	}


	this.showMessages = function(msgs, res, req) {
		if ( (typeof msgs == 'array') && !msgs.length) {
			return;
		}

		$this.dispatchEvent('before_showMessages',{'msgs':msgs,'res':res,'req':req});

		if (req['id']) {
			var image = $this.itemsImage[req['id']];
		} else {
			if (res.items_id) {
				for(var i = 0; i < res.items_id.length; i++) {
					var image = $this.itemsImage[res.items_id[i]];
					break;
				}
			}
		}

		var addedQty = res ? res.added_qty : '';

		var html = '';
		var success = true;
		for(var type in msgs) {
			if (type == 'notice' || type == 'error') {
				success = false;
			}
			for(var i = 0; i < msgs[type].length; i++) {

				switch (type) {
					case 'success' :
						var l = $this.conf.successNotificationTemplate;
						l = l.replace(/{text}/gi, msgs[type][i]);
						if (image) l = l.replace(/{image}/gi, image);
						if (addedQty) l = l.replace(/{qty}/gi, addedQty);

						break;
					default :
						var l = $this.conf.warningNotificationTemplate;
						l = l.replace(/{type}/gi, type);
						l = l.replace(/{text}/gi, msgs[type][i]);
				}

				var t = pjQuery_1_10_2('<div>').append(l);
				if (addedQty <= 1) t.find('.pac-added-qty').empty();
				if (!image) t.find('.pac-img').empty();
				if ($this.conf.isCheckoutPage || req.discount) t.find('.pac-button-hld').remove();

				html += t.html();
			}
		}

		var hld = pjQuery_1_10_2('#pac_notifications').empty().append(html);

		hld.fadeIn(200).mouseenter(function(){
			$this.stopHideMessages();
		}).mouseleave(function(){
			$this.planHideMessages();
		});

		$this.planHideMessages();
		$this.dispatchEvent('after_showMessages',{'msgs':msgs,'res':res,'req':req});
	}

	$this.hideMessages = function() {
		pjQuery_1_10_2('#pac_notifications').fadeOut(500);
		return false;
	}

	$this.stopHideMessages = function() {
		if (typeof pacNoticeHldTimeout != 'undefined') clearTimeout(pacNoticeHldTimeout);
	}

	$this.planHideMessages = function() {
		$this.stopHideMessages();
		pacNoticeHldTimeout = setTimeout(function(){
			$this.hideMessages();
		}, 4000);
	}
	/* End Messages */


	$this.hideAjaxPopup = function() {
		pjQuery_1_10_2.fancybox.close();
		$this.dispatchEvent('after_hideAjaxPopup');
	}

	$this.updateAddButtonsQty = function()
	{
		if (!$this.conf.showQtyOnAddButton) {
			return;
		}
		$this.dispatchEvent('before_updateAddButtonsQty');

		pjQuery_1_10_2('.pac-btn-cart .pac-number').html('').removeClass('pac-visible');
		pjQuery_1_10_2('.pac-btn-cart .pac-icon').html('').removeAttr('style');

		var qs = {};
		for(var i in $this.itemsArh) {
			var it = $this.itemsArh[i];
			if ($this.items[i]) {
				var pId = it['product_id'];
				var qty = it['qty'];
				qs[pId] = (qs[pId]) ? qs[pId] + qty : qty;
				pjQuery_1_10_2('.pac-btn-cart-'+pId).each(function(){
					btn = pjQuery_1_10_2(this);
					if (btn.hasClass('pac-btn-update')) return;
					var q = (qs[pId]  > 99) ? '99+' : qs[pId];
					btn.find('.pac-number').html(q).addClass('pac-visible');
					btn.find('.pac-icon').hide();
				})
			}
		}
		$this.dispatchEvent('after_updateAddButtonsQty');
	}

	$this.getObjLength = function(obj) {
		var c = 0;
		for(var i in obj) {
			c++;
		}
		return c;
	}

	$this.controlMinicart = function(){
		if ($this.conf.mincartIsClosed) {
			$this.showMinicart();
		} else {
			$this.hideMinicart();
		}
	}

	$this.showMinicart = function() {
		$this.dispatchEvent('before_showMinicart');
		if (!$this.conf.micartTemplate) {
			pjQuery_1_10_2('#pac-mini-cart').show();
			pjQuery_1_10_2('.minicart_link').addClass('pac-active').addClass('skip-active');
			if ($this.getObjLength($this.items) > 3 || pjQuery_1_10_2('.pac-mini-products-list').height() > 320) {
				pjQuery_1_10_2('.pac-mini-products-list-hld').jScrollPane();
			}
			$this.conf.mincartIsClosed = false;
			$this.initCartReservation();
		} else {
			pjQuery_1_10_2.fancybox({
				content : pjQuery_1_10_2('#pac-mini-cart'),
				autoResize : true,
				maxWidth : $this.conf.popupWidth,
				wrapCSS : 'fancybox_ajaxcart_minicart',
				openEffect : $this.isAjaxPopupOpen ? 'none' : 'fade',
				afterShow : function(){
					$this.conf.mincartIsClosed = false;
					$this.initCartReservation();

					if ($this.getObjLength($this.items) > 3 || pjQuery_1_10_2('.pac-mini-products-list').height() > 320) {
						pjQuery_1_10_2('.pac-mini-products-list-hld').jScrollPane();
					}

				},
				afterClose : function() {
					$this.conf.mincartIsClosed = true;
					$this.dispatchEvent('after_hideAjaxPopup');
					if (pjQuery_1_10_2('.minicart_head_holder .minicart_link').hasClass('skip-active')) {
						pjQuery_1_10_2('.minicart_head_holder .minicart_link').removeClass('skip-active');
					}
				},
				helpers : {
					overlay : {locked : false}
				}
			});
		}
		$this.dispatchEvent('after_showMinicart');

	}

	$this.hideMinicart = function() {

		$this.dispatchEvent('before_hideMinicart');
		if (!$this.conf.micartTemplate) {
			pjQuery_1_10_2('#pac-mini-cart').hide();
			pjQuery_1_10_2('.minicart_link').removeClass('pac-active').removeClass('skip-active');
		} else {
			$this.hideAjaxPopup();
		}
		$this.conf.mincartIsClosed = true;
		$this.dispatchEvent('after_hideMinicart');
	}

	$this.isAjaxPopupOpen = function(){
		return pjQuery_1_10_2('.fancybox-overlay:visible').length;
	}

	$this.initCartReservation = function()
	{
		if (typeof CartReservationCountDown != 'undefined') {
	       CartReservationCountDown(['.pac-cart-reservation', '.pac-product-info .pac-custom-options']);
	    }
	}

	$this.onDocumentReady = function(firsttime) {
		
		if ($this.conf.isCartPage) _plCartPage();

	    if (!$this.conf.mincartIsClosed) {
	    	$this.showMinicart();
	    }

	    for(var i in $this.onReady) {
	    	var f = $this.onReady[i];
	    	f();
	    }

	    $this.updateAddButtonsQty();

	    if (firsttime && !$this.isMobile()) {
	    	pjQuery_1_10_2('body').click(function(){
	    		if (pjQuery_1_10_2('#pac-mini-cart').is(':hover') !== true && pjQuery_1_10_2('.minicart_link').is(':hover') !== true) {
	    			if (!$this.isAjaxPopupOpen()) {
	    				$this.hideMinicart();
	    			}
	    		}
	    	})
	    }

	    $this.dispatchEvent('onDocumentReady');
	}

	$this.dispatchEvent = function(name, params) {
		if (typeof $this.observers[name] == 'undefined') return;
		if (!params) params={}; params['ajaxCart'] = $this;
		var o = $this.observers[name];
		for (var key in o) {
			o[key](params);
		}

	}

	$this.addObserver = function(name, key, fun) {
		if (typeof $this.observers[name] == 'undefined') {
			$this.observers[name] = {};
		}
		$this.observers[name][key] = fun;
	}

	$this.isMobile = function() {
		return pjQuery_1_10_2(window).width() <= 768;
	}

};
