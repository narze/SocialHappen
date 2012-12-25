describe 'Content View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.ContentView.should.not.be.undefined
    it 'should render template', ->
      $('#app #content-view').text().should.not.be.empty