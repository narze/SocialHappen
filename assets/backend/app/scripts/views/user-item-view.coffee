define [
  'backbone'
  'moment'
  'text!templates/user-item-template.html'
  ], (Backbone, moment, UserItemTemplate) ->

  View = Backbone.View.extend

    tagName: 'tr'
    className: 'user-item'

    initialize: ->
      _.bindAll @
      @model.bind 'change', @render

    render: ->
      # filter & unique & rename
      platforms = _.chain(@model.get('user_platforms'))
              .intersection(['ios','android'])
              .map (platform) ->
                # console.log(platform);
                return 'iOS' if platform is 'ios'
                return 'Android' if platform is 'android'
              .value()
      user = _.clone @model.toJSON()
      user.user_platforms = platforms

      @$el.html _.template(UserItemTemplate, user)
      @

  View