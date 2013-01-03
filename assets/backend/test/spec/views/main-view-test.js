(function() {

  describe('Main View', function() {
    describe('view is loaded', function() {
      it('should initialized view', function() {
        return window.backend.Views.MainView.should.not.be.undefined;
      });
      it('should have #app', function() {
        return $('#app').length.should.equal(1);
      });
      return it('should be rendered', function() {
        window.backend.Views.MainView.rendered.should.be["true"];
        $('#app > #navbar-view').length.should.equal(1);
        $('#app #sidebar-view').length.should.equal(1);
        $('#app #content-view').length.should.equal(1);
        return $('#app #modal').length.should.equal(1);
      });
    });
    return describe('subviews are loaded', function() {
      it('should load navbar view', function() {
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
