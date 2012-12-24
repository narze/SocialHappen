describe 'Main Router', ->
  describe 'router is loaded', ->
    it 'should initialized router', ->
      window.backend.Routers.MainRouter.should.not.be.undefined