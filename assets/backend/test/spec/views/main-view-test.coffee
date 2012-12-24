describe 'Main View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.MainView.should.not.be.undefined