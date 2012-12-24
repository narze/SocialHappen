describe 'Sidebar View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.SidebarView.should.not.be.undefined
    it 'should render template', ->
      $('#app #sidebar-view').text().should.not.be.empty