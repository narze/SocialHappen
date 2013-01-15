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
      return it('should have challenges route', function() {
        return window.backend.Routers.MainRouter.routes['challenges'].should.be.equal('challenges');
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
          return $('#content').find('#users-view').length.should.not.equal(0);
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
            return $('#users-view').find('.user-item').length.should.not.equal(0);
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
          return $('#content').find('#companies-view').length.should.not.equal(0);
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
            return $('#companies-view').find('.company-item').length.should.not.equal(0);
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
            return $('#app #modal .add-credits-modal-view .modal').length.should.not.equal(0);
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
          return $('#content').find('#activities-view').length.should.not.equal(0);
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
            return $('#activities-view').find('.activity-item').length.should.not.equal(0);
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
          return $('#content').find('#challenges-view').length.should.not.equal(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#challenges-view').find('thead').find('th').length.should.equal(4);
          $('#content').find('#challenges-view').find('thead').text().should.match(/Name/);
          $('#content').find('#challenges-view').find('thead').text().should.match(/Start Date/);
          $('#content').find('#challenges-view').find('thead').text().should.match(/End Date/);
          return $('#content').find('#challenges-view').find('thead').text().should.match(/Sonar Box ID/);
        });
        it('should have correct first row of data', function() {
          return $('#content #challenges-view .challenge-item:first td').length.should.equal(4);
        });
        return describe('after data fetched', function() {
          return it('should load each .challenge-item into #challenges-view', function() {
            return $('#challenges-view').find('.challenge-item').length.should.not.equal(0);
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
