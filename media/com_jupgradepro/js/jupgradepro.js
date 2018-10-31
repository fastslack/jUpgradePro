jQuery(function($, undefined) {

  var url1 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.show';
  var url2 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.check';
  var url3 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.step';
  var url4 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.migrate';
  var url5 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.cleanup';
  var url6 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.checks';
  var url7 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.cleantable';
  var url8 = 'index.php?option=com_jupgradepro&format=raw&task=ajax.extensions';

  /*
   * Load the saved planning data to jQuery form
   */
  $.extend({
    printConsole: function(term, message, resume, value) {

      if (typeof resume === 'undefined') {
        resume = true;
      }

      var display = new String(message);

      display = display.replace(/{{NL}}/g, '\n');
      display = display.replace(/{{VARCHAR}}/g, value);
      term.echo(display);

      if (resume == true)
      {
        term.resume();
      }
    }
  });

  /*
   * Print help command
   */
  $.extend({
    printHelp: function(term, command) {

      var split = command.split(' ');

      if (split[1] == 'show')
      {
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_SHOW"));
      }

      if (split[1] == 'check')
      {
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_CHECK"));
      }

      if (split[1] == 'migrate')
      {
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_MIGRATE"));
      }

      if (split[1] == 'composer')
      {
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_COMPOSER"));
      }

      if (!split[1])
      {
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_DESC"));
      }
    }
  });

  /*
   * Update composer
   */
  $.extend({
    updateComposer: function(term, command, spinners) {

      var spinner = spinners['clock'];

      var split = command.split(' ');

      term.pause();

      if (split[1] == 'install')
      {
        var url = 'index.php?option=com_jupgradepro&format=raw&task=ajax.updateComposer';

        term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LINE"));
        term.echo(Joomla.JText._("COM_JUPGRADEPRO_COMPOSER_START"));
        $.start(term, spinner, Joomla.JText._("TEST"));

        $.get(url,	function(result) {

          $.stop(term, spinner);

          if (result !== undefined) {
            $.printConsole(term, result);
          }
        });
      }
      else if (split[1] == 'status')
      {
        var url = 'index.php?option=com_jupgradepro&format=raw&task=ajax.statusComposer';

        $.get(url,	function(result) {

          if (result !== undefined) {
            $.printConsole(term, result);
          }
        });
      }else{
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_COMPOSER"));
      }

    }
  });

  /*
   * Print show command
   */
  $.extend({
    printShow: function(term, command) {

      var split = command.split(' ');

      term.pause();

      if ((split[1] == 'config' && split.length <=2) || split[1] == 'undefined')
      {
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_SHOW"));
      }
      else if (split[1] == 'config')
      {
        url1 = url1 + '&command=config&site=' + split[2];

        $.get(url1,	function(result) {

          if (result !== undefined) {
            $.printConsole(term, result);
          }
        });
      }
      else if (split[1] == 'sites')
      {
        var url1site = url1 + '&command=sites';

        $.get(url1site,	function(result) {

          if (result !== undefined) {
            term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LINE"));
            $.printConsole(term, result);
          }
        });
      }else{
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_SHOW"));
      }
    }
  });

  /*
   * Check site
   */
  $.extend({
    checkSite: function(term, command) {

      var split = command.split(' ');

      term.pause();

      if (split.length <=1 || split[1] == 'undefined')
      {
         $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_CHECK"));
      }
      else if (split[1] != 'undefined')
      {
        var url2site = url2 + '&site=' + split[1];

        $.get(url2site,	function(result) {

          var object = jQuery.parseJSON( result );

          if (object.code >= 500)
          {
            $.printConsole(term, object.message);
            return false;
          }

          if (result !== undefined) {
            $.printConsole(term, object.message);
          }
        });

      }else{
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_COMMAND_"));
      }

    }
  });

  /*
   * Migrate site
   */
  $.extend({
    migrateSite: function(term, command, spinners) {

      var spinner = spinners['clock'];
      var split = command.split(' ');

      term.pause();

      if (split.length <=1 || split[1] == 'undefined')
      {
         $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HELP_MIGRATE"));
      }
      else if (split[1] != 'undefined')
      {
        term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LINE"));

        if (!spinner) {
            this.error('Spinner not found');
        } else {
            $.start(term, spinner, Joomla.JText._("COM_JUPGRADEPRO_CLEANUP_RUNNING"));
        }

        // Cleanup
        var url5site = url5 + '&site=' + split[1];

        $.get(url5site,	function(response5)
        {
          if (response5 !== undefined) {

            var object = jQuery.parseJSON( response5 );

            if (object.code >= 500)
            {
              $.printConsole(term, object.message);
              term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
              $.stop(term, spinner);
              return false;
            }

            $.stop(term, spinner, Joomla.JText._("COM_JUPGRADEPRO_CLEANUP_DONE"));

            setTimeout(function(){
              $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_MIGRATION_METHOD"), false, object.method);
              $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_CURRENT_SITE_VER"), false, object.current_version);
              $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_EXTERNAL_SITE_VER"), false, object.ext_version);

              $.start(term, spinner, Joomla.JText._("COM_JUPGRADEPRO_CHECKS_RUNNING"));

              // Checks
              url6 = url6 + '&site=' + split[1];

              $.get(url6,	function(response6) {
                if (response6 !== undefined) {

                  var object = jQuery.parseJSON( response6 );

                  // Set URL's
                  url3 = url3 + '&site=' + split[1];
                  url4 = url4 + '&site=' + split[1];

                  // Declare promise
                  var p = $.when(1);

                  if (object.number >= 500)
                  {
                    $.printConsole(term, object.text);
                    term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
                    $.stop(term, spinner);
                    return false;
                  }

                  if (object.code >= 500)
                  {
                    $.printConsole(term, object.message);
                    term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
                    $.stop(term, spinner);
                    return false;
                  }

                  if (object.code == 409)
                  {
                    $.printConsole(term, object.message);
                    $.stop(term, spinner);

                    var history = term.history();
                    history.disable();
                    term.push(function(command) {
                      if (command.match(/^(R)$/i)) {

                          setTimeout(function(){
                            term.find('.cursor').hide();
                            $.callStep(term, false, p, split[1], spinners, false, false);
                          }, 2000);
                          term.pop();
                          history.enable();

                      } else if (command.match(/^(A)$/i)) {

                          setTimeout(function(){
                            term.find('.cursor').hide();
                            $.callStep(term, false, p, split[1], spinners, true, false);
                          }, 2000);
                          term.pop();
                          history.enable();

                      } else if (command.match(/^(C)$/i)) {

                          term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
                          term.pop();
                          history.enable();

                      }
                    }, {
                        prompt: '[[g;white;]|] (Run [[gb;orange;](A)]gain / [[gb;green;](R)]esume / [[gb;red;](C)]ancel) ? '
                    });
                  }

                  if (object.code == 200)
                  {
                    $.stop(term, spinner, object.message);

                    setTimeout(function(){
                      $.callStep(term, false, p, split[1], spinners, false);
                    }, 2000);

                  }
                }

              });

            }, 2000);

          }
        });

      }else{
        $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_COMMAND_"));
      }

    }
  });

  /*
   * Start spinning
   */
  $.extend({
    start: function (term, spinner, message) {
      animation = true;
      i = 0;
      function set() {
        var spin = spinner.frames[i++ % spinner.frames.length];
          var text = '[[g;white;]|] ' + spin + ' ' + message;
          term.update(-1, text);
      };
      prompt = term.get_prompt();
      term.find('.cursor').hide();
      term.echo(' ');
      set();
      timer = setInterval(set, spinner.interval);
    }
  });

  /*
   * Stop spinning
   */
  $.extend({
    stop: function (term, spinner, message) {
      setTimeout(function() {
          clearInterval(timer);
          var frame = spinner.frames[i % spinner.frames.length];
          animation = false;
          term.find('.cursor').show();
          if (message)
          {
            term.update(-1, message);
          }
      }, 0);

      //term.update(-1, message);
    }
  });


  $.extend({
    callMigrate: function (term, data, p, sitename, spinners, extensions) {

      if (extensions == true)
      {
        url4site = url4 + '&extensions=tables';
      }else{
        url4site = url4;
      }

      //$.start(term, spinners['dots2']);

      var ret = $.get(url4site,	function(response) {

        var object = jQuery.parseJSON( response );

        if (object.number >= 500)
        {
          $.printConsole(term, object.text);
          term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
          return false;
        }

        if (object !== undefined) {

          var limit = parseInt(object.stop) - parseInt(object.start);

          var text = '•';
          for (i=0;i<=limit;i++)
          {
            text = text + '•';
          }

          promise = p.then(function(){

            setTimeout(function(){
              $.printConsole(term, '[[g;white;]|]  [[g;red;](][[i;yellow;]' + text + '][[g;red;])]', false);
              return $.callStep(term, data, p, sitename, spinners, false, extensions);
            }, 500);

          });
        }

      });

      return ret;
    }
  });

  $.extend({
    callExtensionsCheck: function (term, data, p, sitename, spinners) {

      var url8site = url8 + '&site=' + sitename + '&extensions=check';

      $.get(url8site,	function(response) {

        var object = jQuery.parseJSON( response );

        if (object.code >= 500)
        {
          $.printConsole(term, object.message);
          term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
          return false;
        }

        if (object.code == 200)
        {
          promise = p.then(function(){
            $.printConsole(term, object.message);
          });

          promise = p.then(function(){
            setTimeout(function(){
              return $.callStep(term, data, p, sitename, spinners, false, true);
            }, 500);
          });

          return false;
        }

      });

    }
  });

  $.extend({
    callStep: function (term, data, p, sitename, spinners, cleantable, extensions) {

      if (cleantable == true)
      {
        var url7site = url7 + '&site=' + sitename;

        $.get(url7site,	function(result) {
          if (result === undefined) {
            console.log('Clean table error.')
          }
        });
      }

      if (extensions == true)
      {
        url3site = url3 + '&extensions=tables';
      }else{
        url3site = url3;
      }

      var ret = $.get(url3site,	function(response) {

        var object = jQuery.parseJSON( response );

        if (extensions == true && object.code == 404)
        {
          promise = p.then(function(){
            $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN2"));
            $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_FINISH_STEP"));
            $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
          });

          return false;
        }

        if (object.name == undefined)
        {
          term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN2"));
          //$.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_MIGRATION_FINISHED"));
          //term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));

          promise = p.then(function(){
            setTimeout(function(){
              return $.callExtensionsCheck(term, data, p, sitename, spinners);
            }, 500);
          });

          return false;
        }

        var stop = parseInt(object.cid) + parseInt(object.chunk);

        if (object.total < stop)
        {
          stop = object.total;
        }

        promise = p.then(function(){
          term.echo(Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN2"));
          term.echo('[[g;white;]|]  [[b;green;] Migrating '+object.name+' (Start: '+object.cid+' - Stop: '+stop+' - Total: '+object.total+')');
        });

        if ((object.extension != '0' && object.end == true) || object.code == 404)
        {
          promise = p.then(function(){
            $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN2"));
            $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_FINISH_STEP"));
            $.printConsole(term, Joomla.JText._("COM_JUPGRADEPRO_HORIZONTAL_LIN3"));
          });

          return false;
        }

        if (object.stop != -1)
        {
          promise = p.then(function(){

            setTimeout(function(){
              return $.callMigrate(term, object, p, sitename, spinners, extensions);
            }, 500);

          });
        }else{

          promise = p.then(function(){
            setTimeout(function(){
              return $.callStep(term, data, p, sitename, spinners, false, extensions);
            }, 500);
          });
        }

      });

      return ret;
    }
  });

});
