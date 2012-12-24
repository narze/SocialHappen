(function() {

  describe('NavBar View', function() {
    return describe('view is loaded', function() {
      it('should initialized view', function() {
        return window.backend.Views.NavBarView.should.not.be.undefined;
      });
      return it('should render template', function() {
        return $('#app > #navbar-view').text().should.not.be.empty;
      });
    });
  });

}).call(this);
