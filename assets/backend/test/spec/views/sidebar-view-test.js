(function() {

  describe('Sidebar View', function() {
    return describe('view is loaded', function() {
      it('should initialized view', function() {
        return window.backend.Views.SidebarView.should.not.be.undefined;
      });
      return it('should render template', function() {
        return $('#app #sidebar-view').text().should.not.be.empty;
      });
    });
  });

}).call(this);
