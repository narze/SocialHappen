(function() {

  describe('Sidebar View', function() {
    describe('view is loaded', function() {
      it('should initialized view', function() {
        return window.backend.Views.SidebarView.should.not.be.undefined;
      });
      return it('should render template', function() {
        return $('#app #sidebar-view').text().should.not.be.empty;
      });
    });
    return describe('menus', function() {
      it('should have "Users" menu', function() {
        return $('#sidebar-view').text().should.match(/Users/);
      });
      return it('should have "Activities" menu', function() {
        return $('#sidebar-view').text().should.match(/Activities/);
      });
    });
  });

}).call(this);
