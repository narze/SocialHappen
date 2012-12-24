describe 'Main Router', ->
  describe 'router is loaded', ->
    it 'should initialized router', ->
      window.backend.Routers.MainRouter.should.not.be.undefined
  describe 'routes', ->
    it 'should have default (blank) route', ->
      window.backend.Routers.MainRouter.routes[''].should.not.be.undefined
    it 'should have users route', ->
      window.backend.Routers.MainRouter.routes['users'].should.be.equal 'users'
    it 'should have activities route', ->
      window.backend.Routers.MainRouter.routes['activities'].should.be.equal 'activities'
  describe 'routing', ->
    it 'should not load any views when switched to a bad route', ->
      window.backend.Routers.MainRouter.navigate 'somebadroute', trigger:true
      window.backend.Routers.MainRouter.notFound.should.be.true
    describe 'users', ->
      it 'should load users view when switched route to users', ->
        window.backend.Routers.MainRouter.navigate 'users', trigger:true
        window.backend.Views.UsersView.rendered.should.be.true
      it 'should render #users-view into #content', ->
        $('#content').find('#users-view').length.should.not.equal 0

    describe 'activities', ->
      it 'should load activities view when switched route to activities', ->
        window.backend.Routers.MainRouter.navigate 'activities', trigger:true
        window.backend.Views.UsersView.rendered.should.be.true
      it 'should render #activities-view into #content', ->
        $('#content').find('#activities-view').length.should.not.equal 0