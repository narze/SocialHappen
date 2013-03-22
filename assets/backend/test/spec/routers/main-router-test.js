(function() {

  describe('Main Router', function() {
    describe('router is loaded', function() {
      return it('should initialized router', function() {
        return window.backend.Routers.MainRouter.should.not.be.undefined;
      });
    });
    describe('routes', function() {
      it('should have default (blank) route', function() {
        return window.backend.Routers.MainRouter.routes[''].should.not.be.undefined;
      });
      it('should have users route', function() {
        return window.backend.Routers.MainRouter.routes['users'].should.be.equal('users');
      });
      it('should have activities route', function() {
        return window.backend.Routers.MainRouter.routes['activities'].should.be.equal('activities');
      });
      it('should have companies route', function() {
        return window.backend.Routers.MainRouter.routes['companies'].should.be.equal('companies');
      });
      it('should have challenges route', function() {
        return window.backend.Routers.MainRouter.routes['challenges'].should.be.equal('challenges');
      });
      it('should have rewards route', function() {
        return window.backend.Routers.MainRouter.routes['rewards'].should.be.equal('rewards');
      });
      it('should have devices route', function() {
        return window.backend.Routers.MainRouter.routes['devices'].should.be.equal('devices');
      });
      return it('should have reward-machines route', function() {
        return window.backend.Routers.MainRouter.routes['reward-machines'].should.be.equal('reward-machines');
      });
    });
    return describe('routing', function() {
      it('should not load any views when switched to a bad route', function() {
        window.backend.Routers.MainRouter.navigate('somebadroute', {
          trigger: true
        });
        return window.backend.Routers.MainRouter.notFound.should.be["true"];
      });
      describe('users', function() {
        it('should load users view when switched route to users', function() {
          window.backend.Routers.MainRouter.navigate('users', {
            trigger: true
          });
          return window.backend.Views.UsersView.rendered.should.be["true"];
        });
        it('should set the user menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.users-tab-menu').hasClass('active').should.be["true"];
        });
        it('should have user-item as a subview', function() {
          var subViewName;
          subViewName = 'user-' + window.backend.Collections.UserCollection.models[0].cid;
          return window.backend.Views.UsersView.subViews[subViewName].should.not.be.undefined;
        });
        it('should render #users-view into #content', function() {
          return $('#content').find('#users-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#users-view').find('thead').find('th').length.should.equal(7);
          $('#content').find('#users-view').find('thead').text().should.match(/Name/);
          $('#content').find('#users-view').find('thead').text().should.match(/Facebook/);
          $('#content').find('#users-view').find('thead').text().should.match(/Signup Date/);
          $('#content').find('#users-view').find('thead').text().should.match(/Last Seen/);
          $('#content').find('#users-view').find('thead').text().should.match(/Points/);
          $('#content').find('#users-view').find('thead').text().should.match(/Platforms/);
          return $('#content').find('#users-view').find('thead').text().should.match(/Actions/);
        });
        it('should have correct first row of data', function() {
          $('#content #users-view .user-item:first td').length.should.equal(7);
          $('#content #users-view .user-item:first td').text().should.match(/Noom/);
          return $('#content #users-view .user-item:first td').text().should.match(/Link/);
        });
        return describe('after data fetched', function() {
          return it('should load each .user-item into #users-view', function() {
            return $('#users-view').find('.user-item').length.should.be.above(0);
          });
        });
      });
      describe('companies', function() {
        it('should load companies view when switched route to companies', function() {
          window.backend.Routers.MainRouter.navigate('companies', {
            trigger: true
          });
          return window.backend.Views.CompaniesView.rendered.should.be["true"];
        });
        it('should set the company menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.companies-tab-menu').hasClass('active').should.be["true"];
        });
        it('should render #companies-view into #content', function() {
          return $('#content').find('#companies-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#companies-view').find('thead').find('th').length.should.equal(4);
          $('#content').find('#companies-view').find('thead').text().should.match(/Name/);
          $('#content').find('#companies-view').find('thead').text().should.match(/Created At/);
          $('#content').find('#companies-view').find('thead').text().should.match(/Credits/);
          return $('#content').find('#companies-view').find('thead').text().should.match(/Actions/);
        });
        describe('after data fetched', function() {
          it('should load each .company-item into #companies-view', function() {
            return $('#companies-view').find('.company-item').length.should.be.above(0);
          });
          it('should have correct first row of data', function() {
            $('#content #companies-view .company-item:first td').length.should.equal(4);
            return $('#content #companies-view .company-item:first td').text().should.match(/Figabyte/);
          });
          return it('should have "Add Credits" button', function() {
            $('#content #companies-view .company-item:first td:last').html().should.match(/<button/);
            return $('#content #companies-view .company-item:first td:last').text().should.match(/Add Credits/);
          });
        });
        return describe('adding credits', function() {
          it('Add Credits modal should be hidden at first', function() {
            return $('#app #modal .add-credits-modal-view.modal').length.should.equal(0);
          });
          it('clicking the button should activate modal', function() {
            var subViewName;
            subViewName = 'company-' + window.backend.Collections.CompanyCollection.models[0].cid;
            window.backend.Views.CompaniesView.subViews[subViewName].should.not.be.undefined;
            window.backend.Views.CompaniesView.subViews[subViewName].showAddCreditsModal().should.not.be.undefined;
            return $('#app #modal .add-credits-modal-view .modal').length.should.be.above(0);
          });
          describe('filling 5 credits in modal form', function() {});
          return describe('triggering "save" after filling 5 credits', function() {});
        });
      });
      describe('activities', function() {
        it('should load activities view when switched route to activities', function() {
          window.backend.Routers.MainRouter.navigate('activities', {
            trigger: true
          });
          return window.backend.Views.ActivitiesView.rendered.should.equal(true);
        });
        it('should set the activity menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.activities-tab-menu').hasClass('active').should.be["true"];
        });
        it('should have activity-item as a subview', function() {
          var subViewName;
          subViewName = 'activity-' + window.backend.Collections.ActivityCollection.models[0].cid;
          return window.backend.Views.ActivitiesView.subViews[subViewName].should.not.be.undefined;
        });
        it('should render #activities-view into #content', function() {
          return $('#content').find('#activities-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#activities-view').find('thead').find('th').length.should.equal(5);
          $('#content').find('#activities-view').find('thead').text().should.match(/Date/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Name/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Action/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Company/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Branch/);
          return $('#content').find('#activities-view').find('thead').text().should.match(/Challenge/);
        });
        it('should have correct first row of data', function() {
          $('#content #activities-view .activity-item:first td').length.should.equal(5);
          return $('#content #activities-view .activity-item:first td').text().should.match(/Noom/);
        });
        return describe('after data fetched', function() {
          return it('should load each .activity-item into #activities-view', function() {
            return $('#activities-view').find('.activity-item').length.should.be.above(0);
          });
        });
      });
      describe('challenges', function() {
        it('should load challenges view when switched route to challenges', function() {
          window.backend.Routers.MainRouter.navigate('challenges', {
            trigger: true
          });
          return window.backend.Views.ChallengesView.rendered.should.equal(true);
        });
        it('should set the challenge menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.challenges-tab-menu').hasClass('active').should.be["true"];
        });
        it('should have challenge-item as a subview', function() {
          var subViewName;
          subViewName = 'challenge-' + window.backend.Collections.ChallengeCollection.models[0].cid;
          return window.backend.Views.ChallengesView.subViews[subViewName].should.not.be.undefined;
        });
        it('should render #challenges-view into #content', function() {
          return $('#content').find('#challenges-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#challenges-view').find('thead').find('th').length.should.equal(5);
          $('#content').find('#challenges-view').find('thead').text().should.match(/Name/);
          $('#content').find('#challenges-view').find('thead').text().should.match(/Company/);
          $('#content').find('#challenges-view').find('thead').text().should.match(/Start Date/);
          $('#content').find('#challenges-view').find('thead').text().should.match(/End Date/);
          return $('#content').find('#challenges-view').find('thead').text().should.match(/Sonar Data/);
        });
        it('should have correct first row of data', function() {
          return $('#content #challenges-view .challenge-item:first td').length.should.equal(5);
        });
        return describe('after data fetched', function() {
          return it('should load each .challenge-item into #challenges-view', function() {
            return $('#challenges-view').find('.challenge-item').length.should.be.above(0);
          });
        });
      });
      describe('rewards', function() {
        it('should load rewards view when switched route to rewards', function() {
          window.backend.Routers.MainRouter.navigate('rewards', {
            trigger: true
          });
          return window.backend.Views.RewardsView.rendered.should.equal(true);
        });
        it('should set the reward menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.rewards-tab-menu').hasClass('active').should.be["true"];
        });
        it('should have reward-item as a subview', function() {
          var subViewName;
          subViewName = 'reward-' + window.backend.Collections.RewardCollection.models[0].cid;
          return window.backend.Views.RewardsView.subViews[subViewName].should.not.be.undefined;
        });
        it('should render #rewards-view into #content', function() {
          return $('#content').find('#rewards-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#rewards-view').find('thead').find('th').length.should.equal(5);
          $('#content').find('#rewards-view').find('thead').text().should.match(/Name/);
          $('#content').find('#rewards-view').find('thead').text().should.match(/Point Required/);
          $('#content').find('#rewards-view').find('thead').text().should.match(/Amount/);
          $('#content').find('#rewards-view').find('thead').text().should.match(/Amount Redeemed/);
          return $('#content').find('#rewards-view').find('thead').text().should.match(/Can Play Once/);
        });
        it('should have correct first row of data', function() {
          return $('#content #rewards-view .reward-item:first td').length.should.equal(5);
        });
        describe('after data fetched', function() {
          return it('should load each .reward-item into #rewards-view', function() {
            return $('#rewards-view').find('.reward-item').length.should.be.above(0);
          });
        });
        return describe('reward add view', function() {
          it('should have reward-add-view as a subview', function() {
            return window.backend.Views.RewardsView.subViews['reward-add'].should.not.be.undefined;
          });
          it('should have reward add view', function() {
            return $('#rewards-view').find('#reward-add-view').length.should.be.above(0);
          });
          it('should have form', function() {
            return $('form.reward-add-form').length.should.be.above(0);
          });
          return it('should have labels for each form item', function() {
            var $form;
            $form = $('form.reward-add-form');
            $form.text().should.match(/Name/);
            $form.text().should.match(/Description/);
            $form.text().should.match(/Status/);
            $form.text().should.match(/Redeem Method/);
            $form.text().should.match(/Redeem Date Range/);
            $form.text().should.match(/If not specified, this reward will be redeemable forever/);
            $form.text().should.match(/Quantity/);
            $form.text().should.match(/Amount of reward user can redeem/);
            $form.text().should.match(/Points/);
            $form.text().should.match(/Amount of points user use to redeem this reward/);
            $form.text().should.match(/Redeemable Once/);
            $form.text().should.match(/Each user can redeem this reward once/);
            $form.text().should.match(/Add Reward/);
            return $form.text().should.match(/Cancel/);
          });
        });
      });
      describe('devices', function() {
        it('should load devices view when switched route to devices', function() {
          window.backend.Routers.MainRouter.navigate('devices', {
            trigger: true
          });
          return window.backend.Views.DevicesView.rendered.should.equal(true);
        });
        it('should set the device menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.devices-tab-menu').hasClass('active').should.be["true"];
        });
        it('should have device-item as a subview', function() {
          var subViewName;
          subViewName = 'device-' + window.backend.Collections.DeviceCollection.models[0].cid;
          return window.backend.Views.DevicesView.subViews[subViewName].should.not.be.undefined;
        });
        it('should render #devices-view into #content', function() {
          return $('#content').find('#devices-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#devices-view').find('thead').find('th').length.should.equal(11);
          $('#content').find('#devices-view').find('thead').text().should.match(/ID/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Title/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Status/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Data/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Company/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Challenge/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Branch/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Installed At/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Created At/);
          $('#content').find('#devices-view').find('thead').text().should.match(/Info/);
          return $('#content').find('#devices-view').find('thead').text().should.match(/Actions/);
        });
        it('should have correct first row of data', function() {
          return $('#content #devices-view .device-item:first td').length.should.equal(11);
        });
        describe('after data fetched', function() {
          return it('should load each .device-item into #devices-view', function() {
            return $('#devices-view').find('.device-item').length.should.be.above(0);
          });
        });
        return describe('device add view', function() {
          it('should have device-add-view as a subview', function() {
            return window.backend.Views.DevicesView.subViews['device-add'].should.not.be.undefined;
          });
          it('should have device add view', function() {
            return $('#devices-view').find('#device-add-view').length.should.be.above(0);
          });
          it('should have form', function() {
            return $('form.device-add-form').length.should.be.above(0);
          });
          return it('should have labels for each form item', function() {
            var $form;
            $form = $('form.device-add-form');
            $form.text().should.match(/ID/);
            $form.text().should.match(/Title/);
            $form.text().should.match(/Data/);
            $form.text().should.match(/Company/);
            $form.text().should.match(/Branch/);
            $form.text().should.match(/Add Device/);
            return $form.text().should.match(/Cancel/);
          });
        });
      });
      describe('reward-machines', function() {
        it('should load reward-machines view when switched route to reward-machines', function() {
          window.backend.Routers.MainRouter.navigate('reward-machines', {
            trigger: true
          });
          return window.backend.Views.RewardMachinesView.rendered.should.equal(true);
        });
        it('should set the reward-machine menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.reward-machines-tab-menu').hasClass('active').should.be["true"];
        });
        it('should have reward-machine-item as a subview', function() {
          var subViewName;
          subViewName = 'reward-machine-' + window.backend.Collections.RewardMachineCollection.models[0].cid;
          return window.backend.Views.RewardMachinesView.subViews[subViewName].should.not.be.undefined;
        });
        it('should render #reward-machines-view into #content', function() {
          return $('#content').find('#reward-machines-view').length.should.be.above(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#reward-machines-view').find('thead').find('th').length.should.equal(4);
          $('#content').find('#reward-machines-view').find('thead').text().should.match(/ID/);
          $('#content').find('#reward-machines-view').find('thead').text().should.match(/Name/);
          $('#content').find('#reward-machines-view').find('thead').text().should.match(/Description/);
          return $('#content').find('#reward-machines-view').find('thead').text().should.match(/Location/);
        });
        it('should have correct first row of data', function() {
          return $('#content #reward-machines-view .reward-machine-item:first td').length.should.equal(4);
        });
        describe('after data fetched', function() {
          return it('should load each .reward-machine-item into #reward-machines-view', function() {
            return $('#reward-machines-view').find('.reward-machine-item').length.should.be.above(0);
          });
        });
        return describe('reward-machine add view', function() {
          it('should have reward-machine-add-view as a subview', function() {
            return window.backend.Views.RewardMachinesView.subViews['reward-machine-add'].should.not.be.undefined;
          });
          it('should have reward-machine add view', function() {
            return $('#reward-machines-view').find('#reward-machine-add-view').length.should.be.above(0);
          });
          it('should have form', function() {
            return $('form.reward-machine-add-form').length.should.be.above(0);
          });
          return it('should have labels for each form item', function() {
            var $form;
            $form = $('form.reward-machine-add-form');
            $form.text().should.match(/Name/);
            $form.text().should.match(/Description/);
            $form.text().should.match(/Location/);
            $form.text().should.match(/Add Reward Machine/);
            return $form.text().should.match(/Cancel/);
          });
        });
      });
      return describe('change route back', function() {
        return window.backend.Routers.MainRouter.navigate('', {
          trigger: true
        });
      });
    });
  });

}).call(this);
