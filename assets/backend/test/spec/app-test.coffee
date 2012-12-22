describe 'scripts are loaded', ->
  describe 'require.js is loaded', ->
    it 'should have require function', ->
      require.should.be.a 'function'
  describe 'main is loaded', ->
    it 'should have window.mainLoaded', ->
      expect(window.mainLoaded).to.equal true
  describe 'app is loaded', ->
    it 'should have window.appLoaded', ->
      expect(window.appLoaded).to.equal true