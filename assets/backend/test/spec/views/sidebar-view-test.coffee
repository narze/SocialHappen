describe 'Sidebar View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.SidebarView.should.not.be.undefined
    it 'should render template', ->
      $('#app #sidebar-view').text().should.not.be.empty

  describe 'menus', ->
    it 'should have "Users" menu', ->
      $('#sidebar-view').text().should.match /Users/
    it 'should have "Activities" menu', ->
      $('#sidebar-view').text().should.match /Activities/
    it 'should have "Companies" menu', ->
      $('#sidebar-view').text().should.match /Companies/