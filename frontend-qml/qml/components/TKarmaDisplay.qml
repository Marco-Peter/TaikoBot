import QtQuick
import QtQuick.Controls

// Shows karma value — displays ∞ when karma is null (infinite)
Label {
    property var karma: null   // pass AuthStore.karma or a specific user's karma

    text: (karma === null || karma === undefined) ? "∞" : karma
    font.pixelSize: 16
    font.bold: true
}
