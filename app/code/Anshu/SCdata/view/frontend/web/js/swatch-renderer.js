    define([
    'jquery',
    'jquery/ui',
    'magento-swatch.renderer'
], function ($) {

    $.widget('anshu.SwatchRenderer', $.mage.SwatchRenderer, {
        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                    $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                    $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                    attributeId = $parent.attr('attribute-id'),
                    $input = $parent.find('.' + $widget.options.classes.attributeInput);

            if ($widget.inProductList) {
                $input = $widget.productForm.find(
                        '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                        );
            }

            if ($this.hasClass('disabled')) {
                return;
            }

            if ($this.hasClass('selected')) {
//                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
//                $input.val('');
//                $label.text('');
//                $this.attr('aria-checked', false);
            } else {
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                var flag = 0;
                $('.product-options-wrapper .swatch-attribute').each(function () {
                    var attr = $(this).attr('option-selected');
                    if (typeof attr == "undefined")
                    {
                        flag = 1;
                    }
                });
                $widget._toggleCheckedAttributes($this, $wrapper);


                $widget._Rebuild();
                if (flag == 0)
                {   // Custom Code starts
                    var iname = $widget.options.jsonConfig.sname[this.getProduct()];
                    var imstock = $widget.options.jsonConfig.managestock[this.getProduct()];
                    var idescription = $widget.options.jsonConfig.sdescription[this.getProduct()];
                    var isku = $widget.options.jsonConfig.ssku[this.getProduct()];
                    var finalstockQty = $widget.options.jsonConfig.finalstockQty[this.getProduct()];
                    var managestock = $widget.options.jsonConfig.managestock[this.getProduct()];
                    // console.log(managestock);
                    //var ishortdescription = $widget.options.jsonConfig.sshortdescription[this.getProduct()];
                    //var iadditionalinfo = $widget.options.jsonConfig.sadditionaldata[this.getProduct()];
                    var iurl = $widget.options.jsonConfig.surl[this.getProduct()];
                    var iqty = $widget.options.jsonConfig.sqty[this.getProduct()];
                    var ileadtime = $widget.options.jsonConfig.sleadtime[this.getProduct()];
                    var measurementsold = $widget.options.jsonConfig.measurementsold[this.getProduct()];
                    var finalprice = $widget.options.jsonConfig.finalprice[this.getProduct()];
                    var oldprice = $widget.options.jsonConfig.oldprice[this.getProduct()];
                    var iminqty = $widget.options.jsonConfig.sminqty[this.getProduct()];
                    var measize = $widget.options.jsonConfig.measize[this.getProduct()];
                    var backorderleadtime = $widget.options.jsonConfig.backorderleadtime[this.getProduct()];
                    iqty = iqty / measize;
                    var measurementSoldInUnit = $widget.options.jsonConfig.measurementSoldInUnit[this.getProduct()];
                    iqty = Math.floor(iqty);

                    $('#final_price').val(finalprice);
                    if (idescription != '') {
                        $('[data-role="content"]').find('.description .value').html(idescription);
                    }
                   /* if (ishortdescription != '') {
                        $('.product-info-main').find('.overview .value').html(ishortdescription);
                    } */
                    //for config product left qty
                     if(finalstockQty!='' && managestock==1){
                         munit = 'Only ' + finalstockQty +' '+ measurementsold +' Left!' ; 
                         // console.log(finalstockQty);
                          $('.finalqty').text(munit);  
                    }
                    else{
                       munit=''; 
                        $('.finalqty').text(munit);
                    }

                    if (iname != '') {
                        $('[data-ui-id="page-title-wrapper"]').html(iname);
                    }
                    if(backorderleadtime!=''){
                        $('#backorder_lead_time').html(backorderleadtime);
                    }
                    if(measurementSoldInUnit!=''){
                        $('.measurement-sold-in-unit .value').html(measurementSoldInUnit);
                    }

                    if(measize!=''){
                        $('#measurement_sold_in_size').val(measize);
                        $("#soldsize").text(measize);
                    }
                    if (iminqty != '') {
                        //if ($("input[type=number][name=qty].qty").val() > iminqty) {
                          //  $("input[type=number][name=qty].qty").val($("input[type=number][name=qty].qty").val());
                        //} else {
                            $("input[type=number][name=qty].qty").val(iminqty);
                            $("input[type=number][name=qty].qty").attr('value', iminqty);
                            $("input[type=number][name=qty].qty").attr('min', iminqty);
                        //}
                    }
                    
                   
                    //$('div.product-info-main .sku .value').html(isku);
                    
                    $('.page-product-configurable .product.attribute.sku').css('display', 'block');
                    $('div.product-info-main .sku .value').html('<a href="' + iurl + '">' + isku + '</a>');
                    // $('div.product-info-main .reaminingqty .value').html('<a href="' + iurl + '">' + iremqty + '</a>');
                    var flag = 0;
                    $('.swatch-attribute').each(function () {
                        var attr = $(this).attr('option-selected');
                        if (typeof attr == "undefined")
                        {
                            flag = 1;
                        }
                    });
                 
                    if ($widget.element.parents($widget.options.selectorProduct)
                            .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
                            ) {
                        $widget._UpdatePrice();
                    }

                    $widget._loadMedia();
                    $input.trigger('change');
                    var qty = $('#qty').val();
                    // jQuery('.myli').css('display','none');
                    // jQuery('#div'+config.allowedProducts).toggle();
                    $("#calculator-qty").text(qty);
                    $("#calculator-total-yards").text(qty * measize);
                    $("#calculator-total-feet").text(qty * measize * 3);
                    $("#calculator-total-inches").text(qty * measize * 3 * 12);
                    if ($("#calculator-cut-piece").text() > 0) {
                        var inches = $("#calculator-total-inches").text();
                        var cut = $("#calculator-cut-piece").text();
                        var total = inches / cut;
                        $("#calculator-total-pieces").text(total.toFixed(2)+ ' Pieces');
                    }

                    if(oldprice){
                        $('#old_price').val(oldprice);
                        oldprice = qty * oldprice * measize;
                        $('.old-price').html("<span id='old-price-product-price-34' class='price-wrapper'><span class='old-price-value' style='text-decoration:line-through'>$"+oldprice.toFixed(2)+"</span></span>");
                    } else {
                        $('#old_price').val('');
                        $('.old-price').html('');
                    }      
                }
            }
        }

    });

    return $.anshu.SwatchRenderer;
});
