$.fn.localResizeIMG=function(obj){this.on('change',function(){var file=this.files[0];var URL=URL||webkitURL;var blob=URL.createObjectURL(file);if($.isFunction(obj.before)){obj.before(this,blob,file)};_create(blob,file);this.value=''});function _create(blob){var img=new Image();img.src=blob;img.onload=function(){var that=this;var w=that.width,h=that.height,scale=w/h;w=obj.width||w;h=w/scale;var canvas=document.createElement('canvas');var ctx=canvas.getContext('2d');$(canvas).attr({width:w,height:h});ctx.drawImage(that,0,0,w,h);var base64=canvas.toDataURL('image/jpeg',obj.quality||0.8);if(navigator.userAgent.match(/iphone/i)){var mpImg=new MegaPixImage(img);mpImg.render(canvas,{maxWidth:w,maxHeight:h,quality:obj.quality||0.8});base64=canvas.toDataURL('image/jpeg',obj.quality||0.8)}if(navigator.userAgent.match(/Android/i)){var encoder=new JPEGEncoder();base64=encoder.encode(ctx.getImageData(0,0,w,h),obj.quality*100||80)}var result={blob:blob,base64:base64,clearBase64:base64.substr(base64.indexOf(',')+1)};obj.success(result)}}};