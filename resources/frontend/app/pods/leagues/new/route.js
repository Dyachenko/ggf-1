import Ember from 'ember';

export default Ember.Route.extend({
  model() {
    return this.store.createRecord('league');
  },
  actions: {
    save(league) {
      const flashMessages = Ember.get(this, 'flashMessages');

      let newLeague = this.currentModel.setProperties(league);

      newLeague.validate().then(() => {
        newLeague.save().then(() => {
          flashMessages.success('League has been created');

          this.transitionTo('league.teams', newLeague.id);
        }).catch(() => {
          flashMessages.danger('Unable to create league');
        });
      }).catch((a) => {
        console.log('error= ', a);
        flashMessages.danger('Validation failed');
      });
    }
  },
  deactivate: function() {
    this.currentModel.rollbackAttributes();
  },
});
