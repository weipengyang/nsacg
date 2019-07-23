(function($) {
  'use strict';
  $(function() {
    var $fullText = $('.admin-fullText');
    $('#admin-fullscreen').on('click', function() {
      $.AMUI.fullscreen.toggle();
    });

    $(document).on($.AMUI.fullscreen.raw.fullscreenchange, function() {
      $fullText.text($.AMUI.fullscreen.isFullscreen ? '退出全屏' : '开启全屏');
    });
  });
  var defaluts = {
              foreground: 'red',
              background: 'yellow'
 };
  $.extend({
      alert: function (options) {
          var opts = $.extend({}, defaluts, options); //使用jQuery.extend 覆盖插件默认参数
          var html=[];
          html.push('<div class="am-modal am-modal-alert" tabindex="-1" id="my-alert">');
          html.push('<div class="am-modal-dialog">');
          html.push('<div class="am-modal-hd">');
          html.push(opts.title);
          html.push('</div>');
          html.push('<div class="am-modal-bd">');
          html.push(opts.content);
          html.push('</div>');
          html.push(' <div class="am-modal-footer">');
          html.push('<span class="am-modal-btn">确定</span>');
          html.push('</div>');
          html.push('</div>');
          html.push('</div>');
          $('body').append(html.join(""));
          $('#my-alert').modal();
      }
  });
     })(jQuery);


