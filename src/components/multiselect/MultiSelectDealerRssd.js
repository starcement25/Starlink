import React, { Component } from 'react'
import MultiSelect from 'react-native-multiple-select'
import DataStore from '../../helper/constants/DataStore'
export default class MultiSelectDealerRssd extends Component {
  state = {
    selectedItems: []
  }

  onSelectedItemsChange = async (selectedItems) => { }

  press_me = () => { }

  render() {
    const { selectedItems } = this.state
    var search = 'Search'
    var submit = 'Submit'

    switch (DataStore.language) {
      case 'English':
        search = 'Search'
        submit = 'Submit'
        break
      case 'Hindi':
        search = 'खोज'
        submit = 'जमा करना'
        break
      case 'Assamese':
        search = 'সন্ধান'
        submit = 'দাখিল কৰক'
        break
      case 'Bengali':
        search = 'অনুসন্ধান করুন'
        submit = 'জমা দিন'
        break
    }

    return (
      <MultiSelect
        items={this.props.data.items}
        uniqueKey='id'
        ref={(component) => { this.multiSelect = component }}
        onSelectedItemsChange={this.onSelectedItemsChange}
        onToggleList={this.press_me}
        selectedItems={selectedItems}
        selectText={this.props.data.title}
        searchInputPlaceholderText={search + '...'}
        onChangeInput={(text) => { }}
        tagRemoveIconColor='#CCC'
        tagBorderColor='#CCC'
        tagTextColor='#000'
        selectedItemTextColor='#CCC'
        selectedItemIconColor='#CCC'
        itemTextColor='#000'
        displayKey='name'
        searchInputStyle={{ color: '#CCC' }}
        submitButtonColor='#CCC'
        submitButtonText={submit}
      />
    )
  }
}