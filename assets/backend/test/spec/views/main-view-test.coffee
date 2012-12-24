describe 'Main View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.MainView.should.not.be.undefined
  describe 'subviews are loaded', ->
    it 'should load nav bar view', ->
      window.backend.Views.NavBarView.should.not.be.undefined
    it 'should load sidebar view', ->
      window.backend.Views.SidebarView.should.not.be.undefined
    it 'should load content view', ->
      window.backend.Views.ContentView.should.not.be.undefined