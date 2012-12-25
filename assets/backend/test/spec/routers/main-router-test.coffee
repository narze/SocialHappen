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

      it 'should set the user menu as active', ->
        $('#sidebar-view').find('.main-menu li.users-tab-menu').hasClass('active').should.be.true

      it 'should render #users-view into #content', ->
        $('#content').find('#users-view').length.should.not.equal 0

      it 'should have all fields required', ->
        $('#content').find('#users-view').find('thead').find('th').length.should.equal 7

        $('#content').find('#users-view').find('thead').text().should.match(/Name/)
        $('#content').find('#users-view').find('thead').text().should.match(/Facebook/)
        $('#content').find('#users-view').find('thead').text().should.match(/Signup Date/)
        $('#content').find('#users-view').find('thead').text().should.match(/Last Seen/)
        $('#content').find('#users-view').find('thead').text().should.match(/Points/)
        $('#content').find('#users-view').find('thead').text().should.match(/Platforms/)
        $('#content').find('#users-view').find('thead').text().should.match(/Actions/)

      it 'should have correct first row of data', ->
        $('#content #users-view .user-item:first td').length.should.equal 7
        $('#content #users-view .user-item:first td').text().should.match(/Noom/)
        $('#content #users-view .user-item:first td').text().should.match(/Link/)

      describe 'after data fetched', ->
        it 'should load each .user-item into #users-view', ->
          $('#users-view').find('.user-item').length.should.not.equal 0



    describe 'activities', ->

      it 'should load activities view when switched route to activities', ->
        window.backend.Routers.MainRouter.navigate 'activities', trigger:true
        window.backend.Views.UsersView.rendered.should.be.true

      it 'should set the activity menu as active', ->
        $('#sidebar-view').find('.main-menu li.activities-tab-menu').hasClass('active').should.be.true

      it 'should render #activities-view into #content', ->
        $('#content').find('#activities-view').length.should.not.equal 0

      it 'should have all fields required', ->
        $('#content').find('#activities-view').find('thead').find('th').length.should.equal 6

        $('#content').find('#activities-view').find('thead').text().should.match(/Date/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Name/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Action/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Company/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Branch/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Challenge/)

      it 'should have correct first row of data', ->
        $('#content #activities-view .activity-item:first td').length.should.equal 6
        $('#content #activities-view .activity-item:first td').text().should.match(/Noom/)

      describe 'after data fetched', ->
        it 'should load each .activity-item into #activities-view', ->
          $('#activities-view').find('.activity-item').length.should.not.equal 0

    describe 'change route back', ->
      window.backend.Routers.MainRouter.navigate '', trigger: true