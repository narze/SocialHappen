describe 'Sidebar View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.SidebarView.should.not.be.undefined