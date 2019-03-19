$(document).ready(function (e) {

    //修改时显示
    getEditProductData(function (e) {
        // console.log(e);
        var ue = UE.getEditor('product_content');  //商品详情
        var ue = UE.getEditor('product_brand');  //商品详情
        var imgInputVal = e.ProductImg;
        //显示上传的图片
        $.each(imgInputVal, function (key, value) {
            var uploadImg = '';
            var uploadImg2 = '';
            var is_thumb = value.is_thumb;
            // console.log(value.imgPath);
            uploadImg += '<div class="upload-listDiv">';
            uploadImg += '<input type="hidden" name="imgID[]" value="' + value.id + '">';
            uploadImg += '<img src="/Uploads/Manage/' + value.productimage_url + '" width="120" height="120">';
            uploadImg += '<input type="hidden" name="imgURL[]"  value="' + value.productimage_url + '">';
            uploadImg += '<div class="upload-ldButton" data-url="' + value.productimage_url + '">';
            uploadImg += '<button type="button" onclick="javascript:delImg(this)" name="' + value.id + '"  title="删除图片" class="btn btn-default upload-delete" ><i class="fa fa-trash-o"></i></button>';
            if (is_thumb == 1) {
                uploadImg += '<a class="btn btn-defaule upload-select" onclick="javascript:setThumb(this)" name="' + value.id + '" productid="' + e.id + '" style="float:left;color:red"><i class="fa fa-book" aria-hidden="true">已设为缩略图</i> <input type="hidden" class="isthumb" name="is_thumb[]" value="1"></a >';
            } else {
                uploadImg += '<a class="btn btn-defaule upload-select" onclick="javascript:setThumb(this)" name="' + value.id + '" productid="' + e.id + '" style="float:left"><i class="fa fa-book" aria-hidden="true">设为缩略图</i><input type="hidden" class="isthumb" name="is_thumb[]" value="0"></a >';
            }
            uploadImg += '</div>';
            uploadImg += '</div>';
            $('div#imgShow').append(uploadImg);
        });

        //显示推荐商品
        var category_id = e.category_id;
        var recommendID = e.product_recommend;
        var arr = [];
        // console.log(recommendID);
        if (recommendID != null) {
            var product_recommend = recommendID.split(",");
            // console.log(product_recommend);
            $.post(APP + '/Product/showProductRecommend', {category_id: category_id}, function (res) {
                // console.log(res);
                $('#recommendProduct').html("");

                if (res.code == 200) {
                    $.each(res.data, function (key, value) {
                        arr += `
                                   <input class="recommend" type="checkbox" name="recommendProduct[]"   value=${value.id} />${value.product_name}
                                `;
                    });
                } else if (res.code == 400) {
                    arr = '暂无商品'
                }
                $('#recommendProduct').append(arr);
                //默认选中checked
                $.each($("#recommendProduct").find("input"), function (indexInArray, valueOfElement) {
                    // console.log($(this).val());
                    for (var x = 0; x < product_recommend.length; x++) {
                        if ($(this).val() == product_recommend[x]) {
                            $(this).attr("checked", "checked");
                        }
                    }

                });
            });
        } else {
            arr = '暂无商品'
            $('#recommendProduct').append(arr);
        }


        //显示商品属性  废弃
   /*     var type_id = e.producttype_id;
        $.post(APP + '/Product/productTypeList', {type_id: type_id}, function (data) {
            // console.log(data);
            $('#productType').html("");
            $.each(data, function (key, value) {
                // console.log(key);
                var arr = '';
                $.each(value.productValue, function (key, val) {
                    arr += val.productvalue_name + '，';

                })
                var productAttr = '';
                productAttr += '<div>' + value.productattr_name + '：' + arr + '</div>';
                $('#productType').append(productAttr);
            });
        });*/

        var ruleslist = e.product_parm.split(',');   //设置checkbox选中
        $('.pserve').each(function (index) {
            // console.log(index)
            for (var i = 0; i < ruleslist.length; i++) {
                if ($('.pserve:eq(' + index + ')').attr('value') == ruleslist[i]) {
                    $('.pserve:eq(' + index + ')').attr("checked", true);
                }
            }
        });

        //判断是订金还是全款
        // console.log(e.product_type);
        //订金
        if(e.product_type == 1){
            var str = `
                      <tr class="pro-price">
                        <td align="center">订金价格</td>
                        <td colspan="5"><input type="text" name="product_djprice" id="product_djprice" class="form-control" placeholder="请输入订金价格" value=${e.product_djprice} ></td>
                      </tr>
                        `;
            $('.pro-trBox').hide();
            $('#product_type').parents('tr').after(str);
        }else if(e.product_type == 2){
            $('.pro-Bounds').show();
        }
        //禁止切换付款方式
        $('#product_type').attr("disabled",true);


    });


    // //提交 验证字段
    $('.ajax-post').click(function () {

        //验证推荐商品
        var i = 0;
        $.each($("#recommendProduct").find("input"), function (indexInArray, valueOfElement) {
            // console.log($(this).val());
            if ($(this).attr("checked") == "checked") {
                i++;
            }
        });
        // console.log(i);
        // if (i == 0) {
        //     $.show({
        //         title: '提示',
        //         isConfirm: false,
        //         content: '推荐商品不能为空'
        //     });
        //     return false;
        // }
        // if (i > 4) {
        //     $.show({
        //         title: '提示',
        //         isConfirm: false,
        //         content: '推荐商品不能超过4个'
        //     });
        //     return false;
        // }


        //验证商品属性
        var product_type = $('#product_type').val();
        //订金
        if(product_type == 1){
            var product_djprice = $('#product_djprice').val();
            if(!isPriceNumber(product_djprice) || product_djprice == 0){
                $.show({
                    title: '提示',
                    isConfirm: false,
                    content: '订金价格输入错误'
                });
                return false;
            }
        }
        //全款
        if(product_type == 2){

            //积分额度
            var product_bounds = $('#product_bounds').val();
            if(product_bounds == ''){
                $.show({
                    title: '提示',
                    isConfirm: false,
                    content: '积分额度不能为空'
                });
                return false;
            }

            //属性图片
            var btu_uploads = $('.fa-picture-o').length;
            var attr_img = $('img.pro-imgView').length;
            console.log(btu_uploads);
            console.log(attr_img);
            if(btu_uploads != attr_img){
                $.show({
                    title: '提示',
                    isConfirm: false,
                    content: '属性图片不能为空'
                });
                return false;
            }


            var product_attr = $(".input-group input.form-control");
            var n = 0;
            $.each(product_attr, function (i, value) {
                var attr_name = $(value).attr('name');
                var attr_value = $(value).val();
                // console.log(attr_name);
                // console.log(attr_value);
                //修改 验证属性
                if (attr_name == 'color_name_edit[]') {
                    if (attr_value == '') {
                        n++;
                    }
                }
                if (attr_name == 'attr_name_edit[]') {
                    if (attr_value == '') {
                        n++;
                    }
                }

                // if (attr_name == 'original_price_edit[]') {
                //     if (attr_value == '') {
                //         n++;
                //     } else if (!isPriceNumber(attr_value) || attr_value == 0) {
                //         n++;
                //     }
                // }

                if (attr_name == 'price_edit[]') {
                    if (attr_value == '') {
                        n++;
                    } else if (!isPriceNumber(attr_value) || attr_value == 0) {
                        n++;
                    }
                }
                if (attr_name == 'stock_edit[]') {
                    if (attr_value == '') {
                        n++;
                    } else if (!isPriceNumber(attr_value) || attr_value == 0) {
                        n++;
                    }
                }

                // 添加 验证属性
                if (attr_name == 'color_name[]') {
                    if (attr_value == '') {
                        n++;
                    }
                }
                if (attr_name == 'attr_name[]') {
                    if (attr_value == '') {
                        n++;
                    }
                }

                // if (attr_name == 'original_price[]') {
                //     if (attr_value == '') {
                //         n++;
                //     } else if (!isPriceNumber(attr_value) || attr_value == 0) {
                //         n++;
                //     }
                // }
                
                if (attr_name == 'price[]') {
                    if (attr_value == '') {
                        n++;
                    } else if (!isPriceNumber(attr_value) || attr_value == 0) {
                        n++;
                    }
                }
                if (attr_name == 'stock[]') {
                    if (attr_value == '') {
                        n++;
                    } else if (!isPriceNumber(attr_value) || attr_value == 0) {
                        n++;
                    }
                }

                // console.log(n);
            });

            //验证格式
            if (n != 0) {
                $.show({
                    title: '提示',
                    isConfirm: false,
                    content: '商品属性输入错误，请检查参数'
                });
                return false;
            }
            // return false;
            //end 商品属性
        }



        //验证商品服务
        var check = $('.pserve');
        var checkNumber = 0;
        $.each(check, function (i, value) {
            var checked = $(value).attr('checked');
            if (checked) {
                checkNumber++;
            }
        });
        if (checkNumber == 0) {
            $.show({
                title: '提示',
                isConfirm: false,
                content: '商品服务不能为空'
            });
            return false;
        }

        //验证轮播图
        var sowingMap = $('#imgShow .upload-listDiv');
        var x = 0;
        // console.log(sowingMap);
        if(sowingMap.length == 0){
            $.show({
                title: '提示',
                isConfirm: false,
                content: '轮播图不能为空'
            });
            return false;
        }
        $.each(sowingMap,function (i,val) {
            var isthumb = $(val).find('.isthumb').val();
            if(isthumb == 1){
                x++;
            }
        });
        if(x == 0){
            $.show({
                title: '提示',
                isConfirm: false,
                content: '请设置缩略图'
            });
            return false;
        }



    });


    // 判断金额
    function isPriceNumber(_keyword) {
        if (_keyword == "0" || _keyword == "0." || _keyword == "0.0" || _keyword == "0.00") {
            _keyword = "0";
            return true;
        } else {
            var index = _keyword.indexOf("0");
            var length = _keyword.length;
            if (index == 0 && length > 1) {/*0开头的数字串*/
                var reg = /^[0]{1}[.]{1}[0-9]{1,2}$/;
                if (!reg.test(_keyword)) {
                    return false;
                } else {
                    return true;
                }
            } else {/*非0开头的数字*/
                var reg = /^[1-9]{1}[0-9]{0,10}[.]{0,1}[0-9]{0,2}$/;
                if (!reg.test(_keyword)) {
                    return false;
                } else {
                    return true;
                }
            }
            return false;
        }
    }

    //获取推荐商品
    $('#category_id').change(function () {
        var category_id = $(this).val();
        $.post(APP + '/Product/cateProductList', {category_id: category_id}, function (res) {
            // console.log(res);
            $('#recommendProduct').html("");
            var arr = '';
            if (res.code == 200) {
                $.each(res.data, function (key, value) {
                    arr += `
                                   <input class="recommend" type="checkbox" name="recommendProduct[]"  value=${value.id} />${value.product_name}
                                `;
                });
            } else if (res.code == 400) {
                arr = '暂无商品'
            }
            $('#recommendProduct').append(arr);
        });
    });

    //一级分类禁止选择
    $("#category_id option").each(function (index) {
        // console.log($(this).attr('pid'));
        var pid = $(this).attr('pid');
        if (pid == 0) {
            $(this).attr("disabled", true);
        }
    });

    //新建时的富文本显示
    if ($("#id").val() == '') {
        var ue = UE.getEditor('product_content');
    }

    //切换订金和全款
    $('#product_type').live("change", function () {
        var that = $(this);
        var product_typeID = that.val();
        // alert(product_typeID);
        if (product_typeID == 1) {
            var str = `
                      <tr class="pro-price">
                        <td align="center">订金价格</td>
                        <td colspan="5"><input type="text"  name="product_djprice" id="product_djprice" class="form-control" placeholder="请输入订金价格"/></td>
                      </tr>
                        `;
            $('.pro-trBox').hide();
            $('.pro-Bounds').hide();
            that.parents('tr').after(str);
        }
        if (product_typeID == 2) {
            str = ``;
            $('.pro-trBox').show();
            $('.pro-Bounds').show();
            $('.pro-price').remove();
        }
    });



});