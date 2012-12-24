describe 'Content View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.ContentView.should.not.be.undefined