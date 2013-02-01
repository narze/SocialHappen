describe 'Main Router', ->

  describe 'router is loaded', ->

    it 'should initialized router', ->
      window.backend.Routers.SigninRouter.should.not.be.undefined

  describe 'routes', ->

    it 'should have default (blank) route', ->
      window.backend.Routers.SigninRouter.routes[''].should.be.equal 'signin'

  describe 'routing', ->

    it 'should not load any views when switched to a bad route', ->
      window.backend.Routers.SigninRouter.navigate 'somebadroute', trigger:true
      window.backend.Routers.SigninRouter.notFound.should.be.true

    describe 'signin', ->

      it 'should load signin view when switched route to signin', ->
        window.backend.Routers.SigninRouter.navigate 'devices', trigger:true
        window.backend.Views.SigninView.rendered.should.equal true

      it 'should render #signin-view into #app', ->
        $('#app').find('#signin-view').length.should.not.equal 0

      it 'should have all neccessary items', ->
        $('#app').find('#signin-view').find('input#username').length.should.equal 1
        $('#app').find('#signin-view').find('input#password').length.should.equal 1
        $('#app').find('#signin-view').find('[type=submit]').length.should.equal 1
        $('#app').find('#signin-view').find('#facebook-connect').length.should.equal 1

    describe 'change route back', ->
      window.backend.Routers.SigninRouter.navigate '', trigger: true