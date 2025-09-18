<div class="flex space-x-2">
    <a href="{{ route('users.edit', $user->id) }}" class="px-2 py-1 bg-blue-500 text-white rounded text-sm">Edit</a>
    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded text-sm">Delete</button>
    </form>
</div>
