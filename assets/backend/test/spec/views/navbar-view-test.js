(function() {

  describe('NavBar View', function() {
    describe('view is loaded', function() {
      it('should initialized view', function() {
        return window.backend.Views.NavBarView.should.not.be.undefined;
      });
      return it('should render template', function() {
        return $('#app > #navbar-view').text().should.not.be.empty;
      });
    });
    return describe('Content', function() {
      return it('should have SocialHappen as header text', function() {
        return $('#navbar-view .hidden-phone').text().should.match(/SocialHappen/);
      });
    });
  });

}).call(this);
