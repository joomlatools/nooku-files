/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright      Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           https://github.com/nooku/nooku-platform for the canonical source repository
 */

(function ($)
{
    Attachments = {
        getInstance: function(config)
        {
            var my = {
                init: function (config)
                {
                    my.template = config.template ? config.template : null;
                    my.selector = $(config.selector);
                    my.url = config.url;
                    my.csrf_token = config.csrf_token;
                },
                render: function(attachment, template)
                {
                    var output = '';

                    var template = template ? template : this.template;

                    if (template)
                    {
                        var cleanup = function(content) {
                            return content.replace(/([href|src])="\/\[%=/g, "$1=\"[%=");
                        }

                        var content = cleanup(template);

                        var data = {
                            url: attachment.url,
                            name: this.escape(attachment.name),
                            type: attachment.type,
                            thumbnail: attachment.thumbnail ? attachment.thumbnail.thumbnail : null
                        }

                        output = new EJS({element: content}).render(data);
                    }

                    return output;
                },
                escape: function(string)
                {
                    var entityMap = {
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': '&quot;',
                        "'": '&#39;',
                        "/": '&#x2F;'
                    };

                    return String(string).replace(/[&<>"'\/]/g, function (s) {return entityMap[s]});
                },
                attach: function(attachment)
                {
                    var context = {
                        data: {
                            csrf_token: this.csrf_token,
                            _action: 'attach'
                        },
                        url: this.url,
                        attachment: attachment
                    };

                    this.selector.trigger('before.attach', context);

                    $.ajax({
                        url: context.url,
                        method: 'POST',
                        data: context.data,
                        success: function(data)
                        {
                            context.result = data;
                            my.selector.trigger('after.attach', context);
                        }
                    });
                },
                detach: function(attachment)
                {
                    var context = {
                        data: {
                            csrf_token: this.csrf_token,
                            _action: 'detach'
                        },
                        url: this.url,
                        attachment: attachment
                    };

                    this.selector.trigger('before.detach', context);

                    $.ajax({
                        url: context.url,
                        method: 'POST',
                        data: context.data,
                        success: function(data)
                        {
                            context.result = data;
                            my.selector.trigger('after.detach', context);
                        }
                    });
                },
                replace: function(text, params)
                {
                    $.each(params, function(key, value) {

                        var search = '%7B' + key + '%7D';

                        if (text.search(search) === -1) {
                            search = '{' + key + '}';
                        }

                        text = text.replace(search, my.escape(value));
                    });

                    return text;
                },
                bind: function(event, handler)
                {
                    this.selector.on(event, handler);
                }
            };

            my.init(config);

            return my;
        }
    }
})(kQuery)