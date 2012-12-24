(function() {

  describe('Main View', function() {
    describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.MainView.should.not.be.undefined;
      });
    });
    return describe('subviews are loaded', function() {
      it('should load nav bar view', function() {
        return window.backend.Views.NavBarView.should.not.be.undefined;
      });
      it('should load sidebar view', function() {
        return window.backend.Views.SidebarView.should.not.be.undefined;
      });
      return it('should load content view', function() {
        return window.backend.Views.ContentView.should.not.be.undefined;
      });
    });
  });

}).call(this);
