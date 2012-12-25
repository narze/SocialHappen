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
      return it('should have activities route', function() {
        return window.backend.Routers.MainRouter.routes['activities'].should.be.equal('activities');
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
      describe('activities', function() {
        it('should load activities view when switched route to activities', function() {
          window.backend.Routers.MainRouter.navigate('activities', {
            trigger: true
          });
          return window.backend.Views.UsersView.rendered.should.be["true"];
        });
        it('should set the activity menu as active', function() {
          return $('#sidebar-view').find('.main-menu li.activities-tab-menu').hasClass('active').should.be["true"];
        });
        it('should render #activities-view into #content', function() {
          return $('#content').find('#activities-view').length.should.not.equal(0);
        });
        it('should have all fields required', function() {
          $('#content').find('#activities-view').find('thead').find('th').length.should.equal(6);
          $('#content').find('#activities-view').find('thead').text().should.match(/Date/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Name/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Action/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Company/);
          $('#content').find('#activities-view').find('thead').text().should.match(/Branch/);
          return $('#content').find('#activities-view').find('thead').text().should.match(/Challenge/);
        });
        it('should have correct first row of data', function() {
          $('#content #activities-view .activity-item:first td').length.should.equal(6);
          return $('#content #activities-view .activity-item:first td').text().should.match(/Noom/);
        });
        return describe('after data fetched', function() {
          return it('should load each .activity-item into #activities-view', function() {
            return $('#activities-view').find('.activity-item').length.should.not.equal(0);
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
