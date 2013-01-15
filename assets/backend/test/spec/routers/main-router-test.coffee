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

    it 'should have companies route', ->
      window.backend.Routers.MainRouter.routes['companies'].should.be.equal 'companies'

    it 'should have challenges route', ->
      window.backend.Routers.MainRouter.routes['challenges'].should.be.equal 'challenges'

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

      it 'should have user-item as a subview', ->
        subViewName = 'user-' + window.backend.Collections.UserCollection.models[0].cid
        window.backend.Views.UsersView.subViews[subViewName].should.not.be.undefined

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

    describe 'companies', ->

      it 'should load companies view when switched route to companies', ->
        window.backend.Routers.MainRouter.navigate 'companies', trigger:true
        window.backend.Views.CompaniesView.rendered.should.be.true

      it 'should set the company menu as active', ->
        $('#sidebar-view').find('.main-menu li.companies-tab-menu').hasClass('active').should.be.true

      it 'should render #companies-view into #content', ->
        $('#content').find('#companies-view').length.should.not.equal 0

      it 'should have all fields required', ->
        $('#content').find('#companies-view').find('thead').find('th').length.should.equal 4

        $('#content').find('#companies-view').find('thead').text().should.match(/Name/)
        $('#content').find('#companies-view').find('thead').text().should.match(/Created At/)
        $('#content').find('#companies-view').find('thead').text().should.match(/Credits/)
        $('#content').find('#companies-view').find('thead').text().should.match(/Actions/)

      describe 'after data fetched', ->
        it 'should load each .company-item into #companies-view', ->
          $('#companies-view').find('.company-item').length.should.not.equal 0

        it 'should have correct first row of data', ->
          $('#content #companies-view .company-item:first td').length.should.equal 4
          $('#content #companies-view .company-item:first td').text().should.match(/Figabyte/)

        it 'should have "Add Credits" button', ->
          $('#content #companies-view .company-item:first td:last').html().should.match(/<button/)
          $('#content #companies-view .company-item:first td:last').text().should.match(/Add Credits/)

      describe 'adding credits', ->
        # TODO Trigger before check
        it 'Add Credits modal should be hidden at first', ->
          $('#app #modal .add-credits-modal-view.modal').length.should.equal 0
        it 'clicking the button should activate modal', ->
          # trigger modal
          subViewName = 'company-' + window.backend.Collections.CompanyCollection.models[0].cid
          window.backend.Views.CompaniesView.subViews[subViewName].should.not.be.undefined
          window.backend.Views.CompaniesView.subViews[subViewName].showAddCreditsModal().should.not.be.undefined

          # TODO - it should display correct modal
          $('#app #modal .add-credits-modal-view .modal').length.should.not.equal 0
          # $('#app #modal .add-credits-modal-view .modal').hasClass('hide').should.equal false
        describe 'filling 5 credits in modal form', ->
          # TODO
        describe 'triggering "save" after filling 5 credits', ->
          # TODO
          # it 'should increment 5 credits successfully', ->
          #   subViewName = 'company-' + window.backend.Collections.CompanyCollection.models[0].cid
          #   window.backend.Views.CompaniesView.subViews[subViewName].addCredit()

    describe 'activities', ->

      it 'should load activities view when switched route to activities', ->
        window.backend.Routers.MainRouter.navigate 'activities', trigger:true
        window.backend.Views.ActivitiesView.rendered.should.equal true

      it 'should set the activity menu as active', ->
        $('#sidebar-view').find('.main-menu li.activities-tab-menu').hasClass('active').should.be.true

      it 'should have activity-item as a subview', ->
        subViewName = 'activity-' + window.backend.Collections.ActivityCollection.models[0].cid
        window.backend.Views.ActivitiesView.subViews[subViewName].should.not.be.undefined

      it 'should render #activities-view into #content', ->
        $('#content').find('#activities-view').length.should.not.equal 0

      it 'should have all fields required', ->
        $('#content').find('#activities-view').find('thead').find('th').length.should.equal 5

        $('#content').find('#activities-view').find('thead').text().should.match(/Date/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Name/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Action/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Company/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Branch/)
        $('#content').find('#activities-view').find('thead').text().should.match(/Challenge/)

      it 'should have correct first row of data', ->
        $('#content #activities-view .activity-item:first td').length.should.equal 5
        $('#content #activities-view .activity-item:first td').text().should.match(/Noom/)

      describe 'after data fetched', ->
        it 'should load each .activity-item into #activities-view', ->
          $('#activities-view').find('.activity-item').length.should.not.equal 0

    describe 'challenges', ->

      it 'should load challenges view when switched route to challenges', ->
        window.backend.Routers.MainRouter.navigate 'challenges', trigger:true
        window.backend.Views.ChallengesView.rendered.should.equal true

      it 'should set the challenge menu as active', ->
        $('#sidebar-view').find('.main-menu li.challenges-tab-menu').hasClass('active').should.be.true

      it 'should have challenge-item as a subview', ->
        subViewName = 'challenge-' + window.backend.Collections.ChallengeCollection.models[0].cid
        window.backend.Views.ChallengesView.subViews[subViewName].should.not.be.undefined

      it 'should render #challenges-view into #content', ->
        $('#content').find('#challenges-view').length.should.not.equal 0

      it 'should have all fields required', ->
        $('#content').find('#challenges-view').find('thead').find('th').length.should.equal 4

        $('#content').find('#challenges-view').find('thead').text().should.match(/Name/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/Start Date/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/End Date/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/Sonar Box ID/)

      it 'should have correct first row of data', ->
        $('#content #challenges-view .challenge-item:first td').length.should.equal 4

      describe 'after data fetched', ->
        it 'should load each .challenge-item into #challenges-view', ->
          $('#challenges-view').find('.challenge-item').length.should.not.equal 0

    describe 'change route back', ->
      window.backend.Routers.MainRouter.navigate '', trigger: true