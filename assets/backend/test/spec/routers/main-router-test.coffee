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

    it 'should have rewards route', ->
      window.backend.Routers.MainRouter.routes['rewards'].should.be.equal 'rewards'

    it 'should have devices route', ->
      window.backend.Routers.MainRouter.routes['devices'].should.be.equal 'devices'

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
        $('#content').find('#users-view').length.should.be.above 0

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
          $('#users-view').find('.user-item').length.should.be.above 0

    describe 'companies', ->

      it 'should load companies view when switched route to companies', ->
        window.backend.Routers.MainRouter.navigate 'companies', trigger:true
        window.backend.Views.CompaniesView.rendered.should.be.true

      it 'should set the company menu as active', ->
        $('#sidebar-view').find('.main-menu li.companies-tab-menu').hasClass('active').should.be.true

      it 'should render #companies-view into #content', ->
        $('#content').find('#companies-view').length.should.be.above 0

      it 'should have all fields required', ->
        $('#content').find('#companies-view').find('thead').find('th').length.should.equal 4

        $('#content').find('#companies-view').find('thead').text().should.match(/Name/)
        $('#content').find('#companies-view').find('thead').text().should.match(/Created At/)
        $('#content').find('#companies-view').find('thead').text().should.match(/Credits/)
        $('#content').find('#companies-view').find('thead').text().should.match(/Actions/)

      describe 'after data fetched', ->
        it 'should load each .company-item into #companies-view', ->
          $('#companies-view').find('.company-item').length.should.be.above 0

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
          $('#app #modal .add-credits-modal-view .modal').length.should.be.above 0
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
        $('#content').find('#activities-view').length.should.be.above 0

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
          $('#activities-view').find('.activity-item').length.should.be.above 0

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
        $('#content').find('#challenges-view').length.should.be.above 0

      it 'should have all fields required', ->
        $('#content').find('#challenges-view').find('thead').find('th').length.should.equal 5

        $('#content').find('#challenges-view').find('thead').text().should.match(/Name/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/Company/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/Start Date/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/End Date/)
        $('#content').find('#challenges-view').find('thead').text().should.match(/Sonar Data/)

      it 'should have correct first row of data', ->
        $('#content #challenges-view .challenge-item:first td').length.should.equal 5

      describe 'after data fetched', ->
        it 'should load each .challenge-item into #challenges-view', ->
          $('#challenges-view').find('.challenge-item').length.should.be.above 0

    describe 'rewards', ->

      it 'should load rewards view when switched route to rewards', ->
        window.backend.Routers.MainRouter.navigate 'rewards', trigger:true
        window.backend.Views.RewardsView.rendered.should.equal true

      it 'should set the reward menu as active', ->
        $('#sidebar-view').find('.main-menu li.rewards-tab-menu').hasClass('active').should.be.true

      it 'should have reward-item as a subview', ->
        subViewName = 'reward-' + window.backend.Collections.RewardCollection.models[0].cid
        window.backend.Views.RewardsView.subViews[subViewName].should.not.be.undefined

      it 'should render #rewards-view into #content', ->
        $('#content').find('#rewards-view').length.should.be.above 0

      it 'should have all fields required', ->
        $('#content').find('#rewards-view').find('thead').find('th').length.should.equal 5

        $('#content').find('#rewards-view').find('thead').text().should.match(/Name/)
        $('#content').find('#rewards-view').find('thead').text().should.match(/Point Required/)
        $('#content').find('#rewards-view').find('thead').text().should.match(/Amount/)
        $('#content').find('#rewards-view').find('thead').text().should.match(/Amount Redeemed/)
        $('#content').find('#rewards-view').find('thead').text().should.match(/Can Play Once/)

      it 'should have correct first row of data', ->
        $('#content #rewards-view .reward-item:first td').length.should.equal 5

      describe 'after data fetched', ->
        it 'should load each .reward-item into #rewards-view', ->
          $('#rewards-view').find('.reward-item').length.should.be.above 0

      describe 'reward add view', ->
        it 'should have reward-add-view as a subview', ->
          window.backend.Views.RewardsView.subViews['reward-add'].should.not.be.undefined

        it 'should have reward add view', ->
          $('#rewards-view').find('#reward-add-view').length.should.be.above 0

        it 'should have form', ->
          $('form.reward-add-form').length.should.be.above 0

        it 'should have labels for each form item', ->
          $form = $('form.reward-add-form')
          $form.text().should.match /Name/
          $form.text().should.match /Description/
          # $form.text().should.match /Image/
          $form.text().should.match /Status/
          $form.text().should.match /Redeem Method/
          $form.text().should.match /Redeem Date Range/
          $form.text().should.match /If not specified, this reward will be redeemable forever/
          $form.text().should.match /Quantity/
          $form.text().should.match /Amount of reward user can redeem/
          $form.text().should.match /Points/
          $form.text().should.match /Amount of points user use to redeem this reward/
          $form.text().should.match /Redeemable Once/
          $form.text().should.match /Each user can redeem this reward once/
          $form.text().should.match /Add Reward/
          $form.text().should.match /Cancel/

    describe 'devices', ->

      it 'should load devices view when switched route to devices', ->
        window.backend.Routers.MainRouter.navigate 'devices', trigger:true
        window.backend.Views.DevicesView.rendered.should.equal true

      it 'should set the device menu as active', ->
        $('#sidebar-view').find('.main-menu li.devices-tab-menu').hasClass('active').should.be.true

      it 'should have device-item as a subview', ->
        subViewName = 'device-' + window.backend.Collections.DeviceCollection.models[0].cid
        window.backend.Views.DevicesView.subViews[subViewName].should.not.be.undefined

      it 'should render #devices-view into #content', ->
        $('#content').find('#devices-view').length.should.be.above 0

      it 'should have all fields required', ->
        $('#content').find('#devices-view').find('thead').find('th').length.should.equal 11

        $('#content').find('#devices-view').find('thead').text().should.match(/ID/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Title/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Status/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Data/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Company/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Challenge/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Branch/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Installed At/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Created At/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Info/)
        $('#content').find('#devices-view').find('thead').text().should.match(/Actions/)


      it 'should have correct first row of data', ->
        $('#content #devices-view .device-item:first td').length.should.equal 11

      describe 'after data fetched', ->
        it 'should load each .device-item into #devices-view', ->
          $('#devices-view').find('.device-item').length.should.be.above 0

      describe 'device add view', ->
        it 'should have device-add-view as a subview', ->
          window.backend.Views.DevicesView.subViews['device-add'].should.not.be.undefined

        it 'should have device add view', ->
          $('#devices-view').find('#device-add-view').length.should.be.above 0

        it 'should have form', ->
          $('form.device-add-form').length.should.be.above 0

        it 'should have labels for each form item', ->
          $form = $('form.device-add-form')
          $form.text().should.match /ID/
          $form.text().should.match /Title/
          $form.text().should.match /Data/
          $form.text().should.match /Company/
          $form.text().should.match /Branch/
          $form.text().should.match /Add Device/
          $form.text().should.match /Cancel/

    describe 'change route back', ->
      window.backend.Routers.MainRouter.navigate '', trigger: true