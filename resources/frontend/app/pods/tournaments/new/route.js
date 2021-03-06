import Ember from 'ember';

export default Ember.Route.extend({
  model() {
    return this.store.createRecord('tournament', {
      'type': 'league',
      'membersType': 'single'
    });
  },

  actions: {
    save(tournament) {
      const flashMessages = Ember.get(this, 'flashMessages');

      let newTournament = this.currentModel.setProperties(tournament);

      newTournament.validate().then(() => {
        newTournament.save().then(() => {
          flashMessages.success('Tournament has been created');

          this.transitionTo('tournament.teams', newTournament.id);
        }).catch(() => {
          flashMessages.danger('Unable to create tournament');
        });
      }).catch(() => {
        flashMessages.danger('Validation failed');
      });
    }
  },

  deactivate: function() {
    this.currentModel.rollbackAttributes();
  },
});
