describe 'Main View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.MainView.should.not.be.undefined
    it 'should have #app', ->
      $('#app').length.should.equal 1
    it 'should be rendered', ->
      window.backend.Views.MainView.rendered.should.be.true
      $('#app > #navbar-view').length.should.equal 1
      $('#app > #sidebar-view').length.should.equal 1
      $('#app > #content-view').length.should.equal 1
  describe 'subviews are loaded', ->
    it 'should load navbar view', ->
      window.backend.Views.NavBarView.should.not.be.undefined
    it 'should load sidebar view', ->
      window.backend.Views.SidebarView.should.not.be.undefined
    it 'should load content view', ->
      window.backend.Views.ContentView.should.not.be.undefined
