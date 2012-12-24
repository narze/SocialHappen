(function() {

  describe('Sidebar View', function() {
    return describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.SidebarView.should.not.be.undefined;
      });
    });
  });

}).call(this);
