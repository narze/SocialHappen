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
        return it('should render #users-view into #content', function() {
          return $('#content').find('#users-view').length.should.not.equal(0);
        });
      });
      return describe('activities', function() {
        it('should load activities view when switched route to activities', function() {
          window.backend.Routers.MainRouter.navigate('activities', {
            trigger: true
          });
          return window.backend.Views.UsersView.rendered.should.be["true"];
        });
        return it('should render #activities-view into #content', function() {
          return $('#content').find('#activities-view').length.should.not.equal(0);
        });
      });
    });
  });

}).call(this);
